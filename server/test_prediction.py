#!/usr/bin/env python3
import json
import sys
import os

# Add current directory to path
sys.path.append(os.path.dirname(__file__))

# Import the prediction function
from predict_enrollment import calculate_likelihood_from_programs

def test_prediction():
    # Test data
    test_data = {
        "program": "BSN",
        "second_program": "BSCS", 
        "third_program": "BSIT",
        "applying_for": "Freshman",
        "family_income": "250000",
        "sex": "Female",
        "nationality": "Filipino"
    }
    
    likelihood = calculate_likelihood_from_programs(
        test_data["program"],
        test_data["second_program"], 
        test_data["third_program"],
        test_data["applying_for"],
        test_data["family_income"],
        test_data["sex"],
        test_data["nationality"]
    )
    
    result = {
        "likelihood": round(likelihood, 2),
        "status": "success",
        "test_data": test_data
    }
    
    print(json.dumps(result, indent=2))

if __name__ == "__main__":
    test_prediction()