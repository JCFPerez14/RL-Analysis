import os
import pickle
import numpy as np
import pandas as pd
from sklearn.metrics import accuracy_score, f1_score, roc_auc_score, classification_report, confusion_matrix

MODEL_PATH = '88p_sir_ap_best_enrollment_model.pkl'
DATA_PATH = 'dataset_with_engineered_features.csv'  # fallback dataset with features + target
TARGET_CANDIDATES = ['Enrolled_encoded', 'Enrolled']

assert os.path.exists(MODEL_PATH), f'Model file not found: {MODEL_PATH}'
assert os.path.exists(DATA_PATH), f'Data file not found: {DATA_PATH}'

def try_load_model(path):
    errors = []
    # 1) Standard pickle
    try:
        with open(path, 'rb') as f:
            return pickle.load(f)
    except Exception as e:
        errors.append(('pickle', repr(e)))
    # 2) Pickle with latin1 encoding (helps with Python2/3 or bytes/str issues)
    try:
        with open(path, 'rb') as f:
            return pickle.load(f, encoding='latin1')
    except Exception as e:
        errors.append(('pickle_latin1', repr(e)))
    # 3) joblib
    try:
        import joblib
        return joblib.load(path)
    except Exception as e:
        errors.append(('joblib', repr(e)))
    # 4) cloudpickle
    try:
        import cloudpickle
        with open(path, 'rb') as f:
            return cloudpickle.load(f)
    except Exception as e:
        errors.append(('cloudpickle', repr(e)))
    # 5) dill
    try:
        import dill
        with open(path, 'rb') as f:
            return dill.load(f)
    except Exception as e:
        errors.append(('dill', repr(e)))
    raise RuntimeError('Failed to load model with all strategies', errors)

# Probe file size for sanity
print('Model file size (bytes):', os.path.getsize(MODEL_PATH))

model = try_load_model(MODEL_PATH)

print('Loaded model type:', type(model))
# Some models (e.g., sklearn Pipeline) expose steps; print briefly if present
if hasattr(model, 'steps'):
    print('Pipeline steps:', [name for name, _ in model.steps])

# Load data
df = pd.read_csv(DATA_PATH)
print('Data shape:', df.shape)

# Try to find target column
target_col = None
for col in TARGET_CANDIDATES:
    if col in df.columns:
        target_col = col
        break

if target_col is None:
    raise ValueError(f'None of the target columns found in data: {TARGET_CANDIDATES}')

# Prepare X, y
y = df[target_col]
X = df.drop(columns=[c for c in TARGET_CANDIDATES if c in df.columns])

# Convert object dtypes to category to be friendly with LightGBM/CatBoost if needed
for c in X.select_dtypes(include=['object']).columns:
    X[c] = X[c].astype('category')

# Align columns to the model's known features if available
feature_names = None
if hasattr(model, 'feature_names_in_'):
    feature_names = list(model.feature_names_in_)
elif hasattr(model, 'get_booster') and hasattr(model, 'n_features_in_'):
    # XGBoost sklearn wrapper after fit has n_features_in_, but not names; keep current X
    feature_names = None
elif hasattr(model, 'feature_name_'):
    # Some models expose feature_name_
    try:
        feature_names = list(model.feature_name_)
    except Exception:
        feature_names = None

if feature_names is not None:
    missing = [c for c in feature_names if c not in X.columns]
    if missing:
        print('Warning: Missing expected features in data:', missing)
    # Subset and order columns if possible
    existing = [c for c in feature_names if c in X.columns]
    X = X[existing]

print('Final X shape:', X.shape)
print('y distribution:', y.value_counts(normalize=True).round(3).to_dict())

# Simple train/test split for evaluation without leakage of metrics (80/20 split)
from sklearn.model_selection import train_test_split
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42, stratify=y if len(np.unique(y)) > 1 else None
)

# Predict on test split
y_pred = model.predict(X_test)

# Try to get probabilities for ROC AUC if available
y_proba = None
if hasattr(model, 'predict_proba'):
    try:
        y_proba = model.predict_proba(X_test)
        # If 2D, take positive class column
        if isinstance(y_proba, np.ndarray) and y_proba.ndim == 2 and y_proba.shape[1] >= 2:
            y_score = y_proba[:, 1]
        else:
            y_score = y_proba
    except Exception as e:
        print('predict_proba failed:', e)
        y_score = None
else:
    y_score = None

acc = accuracy_score(y_test, y_pred)
f1 = f1_score(y_test, y_pred, zero_division=0)
roc = None
if y_score is not None and len(np.unique(y_test)) > 1:
    try:
        roc = roc_auc_score(y_test, y_score)
    except Exception as e:
        print('roc_auc_score failed:', e)

print({'accuracy': acc, 'f1': f1, 'roc_auc': roc})
print('Classification report:', classification_report(y_test, y_pred, zero_division=0))
print('Confusion matrix:', confusion_matrix(y_test, y_pred))