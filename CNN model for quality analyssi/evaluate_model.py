import tensorflow as tf
from tensorflow.keras.preprocessing.image import ImageDataGenerator

# Load the trained model
model = tf.keras.models.load_model("vegetable_model.h5")

# Define the test dataset directory
TEST_DATASET_PATH = r"D:\zasprin\cnn1 model for quality analyssi\test_dataset"  # Update this if your test dataset is in a different location
IMG_SIZE = (224, 224)
BATCH_SIZE = 32

# Data generator for test dataset (No augmentation, only rescaling)
test_datagen = ImageDataGenerator(rescale=1.0/255)

# Load test dataset
test_generator = test_datagen.flow_from_directory(
    TEST_DATASET_PATH,
    target_size=IMG_SIZE,
    batch_size=BATCH_SIZE,
    class_mode="categorical",
    shuffle=False  # No shuffling to correctly match predictions with labels
)

# Evaluate model on test data
loss, accuracy = model.evaluate(test_generator)

# Display the results
print(f"✅ Test Accuracy: {accuracy * 100:.2f}%")
print(f"🔹 Test Loss: {loss:.4f}")
