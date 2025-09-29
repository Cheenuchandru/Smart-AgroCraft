import tensorflow as tf
from tensorflow.keras.applications import MobileNetV2
from tensorflow.keras.models import Model
from tensorflow.keras.layers import Dense, GlobalAveragePooling2D
from tensorflow.keras.preprocessing.image import ImageDataGenerator

# Define dataset path
DATASET_PATH = "D:\zasprin\cnn1 model for quality analyssi\dataset"  # Update this with your dataset path
IMG_SIZE = (224, 224)
BATCH_SIZE = 32

# Automatically detect the number of classes
train_datagen = ImageDataGenerator(rescale=1.0/255, validation_split=0.2)
train_generator = train_datagen.flow_from_directory(
    DATASET_PATH, target_size=IMG_SIZE, batch_size=BATCH_SIZE, class_mode="categorical", subset="training"
)

NUM_CLASSES = train_generator.num_classes  # Automatically set the number of classes

# Load MobileNetV2 without top layers
base_model = MobileNetV2(weights="imagenet", include_top=False, input_shape=(224, 224, 3))
base_model.trainable = False  # Freeze base model layers

# Add custom layers
x = base_model.output
x = GlobalAveragePooling2D()(x)
x = Dense(128, activation="relu")(x)
output_layer = Dense(NUM_CLASSES, activation="softmax")(x)  # Corrected class count

# Create model
model = Model(inputs=base_model.input, outputs=output_layer)

# Compile the model
model.compile(optimizer="adam", loss="categorical_crossentropy", metrics=["accuracy"])

# Validation data
val_generator = train_datagen.flow_from_directory(
    DATASET_PATH, target_size=IMG_SIZE, batch_size=BATCH_SIZE, class_mode="categorical", subset="validation"
)

# Train the model
model.fit(train_generator, validation_data=val_generator, epochs=10)

# Save the trained model
model.save("vegetable_model.h5")

print(f"✅ Model training complete with {NUM_CLASSES} classes and saved as 'vegetable_model.h5'")
