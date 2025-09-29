from flask import Flask, request, jsonify
import tensorflow as tf
import numpy as np
import cv2
import os
from werkzeug.utils import secure_filename

app = Flask(__name__)

UPLOAD_FOLDER = "static/uploads"
app.config["UPLOAD_FOLDER"] = UPLOAD_FOLDER
app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024  # 16MB limit

if not os.path.exists(UPLOAD_FOLDER):
    os.makedirs(UPLOAD_FOLDER)

MODEL_PATH = "vegetable_model.h5"

if not os.path.exists(MODEL_PATH):
    raise FileNotFoundError(f"Error: Model file '{MODEL_PATH}' not found.")

model = tf.keras.models.load_model(MODEL_PATH)

class_labels = [
    "Bad Quality Brokoli", "Bad Quality Cabbage", "Bad Quality Capsicum",
    "Bad Quality Carrot", "Bad Quality Cauliflower", "Bad Quality Potato",
    "Bad Quality Tomato", "Bad Quality GreenChilli", "Good Quality Brokoli",
    "Good Quality Cabbage", "Good Quality Capsicum", "Good Quality Carrot",
    "Good Quality Cauliflower", "Good Quality GreenChilli", "Good Quality Potato",
    "Good Quality Tomato"
]

def preprocess_image(image_path):
    img = cv2.imread(image_path)
    if img is None:
        raise ValueError("Error: Unable to read the uploaded image. Please upload a valid image file.")

    img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
    img = cv2.resize(img, (224, 224))
    img = img / 255.0
    img = np.expand_dims(img, axis=0)
    return img

@app.route("/predict", methods=["POST"])
def predict():
    if "file" not in request.files:
        return jsonify({"error": "No file uploaded"}), 400

    file = request.files["file"]

    if file.filename == "":
        return jsonify({"error": "No selected file"}), 400

    if file:
        filename = secure_filename(file.filename)
        file_path = os.path.join(app.config["UPLOAD_FOLDER"], filename)
        file.save(file_path)

        try:
            img = preprocess_image(file_path)
            prediction = model.predict(img)
            predicted_class = class_labels[np.argmax(prediction)]

            # Check if the vegetable is of good quality
            if predicted_class.startswith("Good Quality"):
                return jsonify({"prediction": predicted_class, "file_path": file_path})
            else:
                return jsonify({"error": "Please provide a good quality vegetable!"})

        except Exception as e:
            return jsonify({"error": str(e)}), 500

if __name__ == "__main__":
    app.run(debug=True)
