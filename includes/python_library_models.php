<?php
declare(strict_types=1);

return <<<'CURRICULUM'
@@ scikit-learn | Your first classifier and a held-out baseline
A feature matrix X contains one row per observation. A target vector y records the answers used for training. Split before fitting and use a dummy baseline to judge whether the model adds value. Stratification keeps class proportions similar.
Change the split seed and compare model versus baseline accuracy. Discuss why a single test split can be noisy.
Which data should be held out from fitting? | The test set | The training set | Every feature used by the model
---
from sklearn.datasets import load_iris
from sklearn.dummy import DummyClassifier
from sklearn.linear_model import LogisticRegression
from sklearn.model_selection import train_test_split

X, y = load_iris(return_X_y=True)
X_train, X_test, y_train, y_test = train_test_split(X, y, stratify=y, test_size=.25, random_state=42)
baseline = DummyClassifier(strategy="most_frequent").fit(X_train, y_train)
model = LogisticRegression(max_iter=1000).fit(X_train, y_train)
print(baseline.score(X_test, y_test), model.score(X_test, y_test))
assert model.score(X_test, y_test) > baseline.score(X_test, y_test)
@@ scikit-learn | Regression, regularization, and error metrics
Regression models predict numeric values. Ridge shrinks coefficients with an L2 penalty. Scaling ensures the penalty treats comparable feature changes similarly. MAE reports error in the original target units.
Compare Ridge with DummyRegressor. Report MAE on the same split and explain what its units mean.
What units does mean absolute error use? | The units of the target | Squared target units | No units
---
from sklearn.datasets import make_regression
from sklearn.linear_model import Ridge
from sklearn.metrics import mean_absolute_error
from sklearn.model_selection import train_test_split
from sklearn.pipeline import make_pipeline
from sklearn.preprocessing import StandardScaler

X, y = make_regression(n_samples=300, n_features=4, noise=12, random_state=42)
X_train, X_test, y_train, y_test = train_test_split(X, y, random_state=42)
model = make_pipeline(StandardScaler(), Ridge(alpha=1))
model.fit(X_train, y_train)
print(round(mean_absolute_error(y_test, model.predict(X_test)), 2))
@@ scikit-learn | Mixed feature pipelines and leakage
ColumnTransformer applies different preparation to numeric and categorical columns. Put the transformer inside the pipeline so each training fold learns its own imputation and encoding. Unknown categories can be handled during inference without fitting on new observations.
Add another missing numeric value and an unseen store. Inspect the output feature names with get_feature_names_out().
Where should learned preprocessing live during cross-validation? | Inside the estimator pipeline | On the combined dataset before splitting | Only in the test folds
---
import pandas as pd
from sklearn.compose import ColumnTransformer
from sklearn.impute import SimpleImputer
from sklearn.linear_model import LogisticRegression
from sklearn.pipeline import make_pipeline
from sklearn.preprocessing import OneHotEncoder, StandardScaler

X = pd.DataFrame({"age": [20, 42, None, 50, 25, 60], "store": ["A", "B", "A", "B", "A", "B"]})
y = [0, 1, 0, 1, 0, 1]
numeric = make_pipeline(SimpleImputer(strategy="median"), StandardScaler())
prepare = ColumnTransformer([("numeric", numeric, ["age"]), ("store", OneHotEncoder(handle_unknown="ignore"), ["store"])])
model = make_pipeline(prepare, LogisticRegression()).fit(X, y)
print(model.predict_proba(pd.DataFrame({"age": [41], "store": ["C"]})))
@@ scikit-learn | Trees, random forests, and cross-validation
A decision tree recursively splits features. A random forest averages many randomized trees to reduce variance. Cross-validation measures the score across multiple train/validation splits. Compare mean and variation using identical splits.
Compare a depth-limited tree and forest with the same folds. Record training time as well as balanced accuracy.
Why evaluate competing models with identical folds? | To reduce variation caused by different splits | To give them identical weights | To skip fitting the estimators
---
from sklearn.datasets import load_wine
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import StratifiedKFold, cross_val_score
from sklearn.tree import DecisionTreeClassifier

X, y = load_wine(return_X_y=True)
folds = StratifiedKFold(5, shuffle=True, random_state=42)
for estimator in [DecisionTreeClassifier(max_depth=3, random_state=42),
                  RandomForestClassifier(n_estimators=80, random_state=42)]:
    scores = cross_val_score(estimator, X, y, cv=folds, scoring="balanced_accuracy")
    print(type(estimator).__name__, scores.mean().round(3), scores.std().round(3))
@@ scikit-learn | Grid search with independent evaluation
GridSearchCV compares candidate hyperparameters on training folds. Keep a separate test set for final evaluation after the search. Pipeline parameter names use the step name followed by two underscores and the parameter name.
Add an SVM gamma grid. Compare search cost and nested cross-validation as a protocol when you cannot afford a large test holdout.
Which set should be used for the final reported estimate? | The untouched test set | The best grid-search training fold | The full data before splitting
---
from sklearn.datasets import load_digits
from sklearn.model_selection import GridSearchCV, train_test_split
from sklearn.pipeline import make_pipeline
from sklearn.preprocessing import StandardScaler
from sklearn.svm import SVC

X, y = load_digits(return_X_y=True)
X_train, X_test, y_train, y_test = train_test_split(X, y, stratify=y, random_state=42)
search = GridSearchCV(make_pipeline(StandardScaler(), SVC()), {"svc__C": [.1, 1, 10]}, cv=3)
search.fit(X_train, y_train)
print(search.best_params_, round(search.score(X_test, y_test), 3))
@@ scikit-learn | Imbalanced classes, thresholds, and precision-recall
Accuracy can hide poor minority-class performance. Precision measures how often alerts are correct, while recall measures how many positives are caught. Choose a threshold using validation data and an explicit objective, then report test performance once.
Change the objective to favor recall. Record the effect on false alarms at the chosen threshold.
Why choose a threshold on validation data? | Choosing it on test labels biases the final estimate | Thresholds can only take integer values | Precision is independent of thresholds
---
from sklearn.datasets import make_classification
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import average_precision_score, f1_score, precision_recall_curve
from sklearn.model_selection import train_test_split
import numpy as np

X, y = make_classification(n_samples=1000, weights=[.92, .08], random_state=42)
X_train, X_hold, y_train, y_hold = train_test_split(X, y, test_size=.4, stratify=y, random_state=42)
X_val, X_test, y_val, y_test = train_test_split(X_hold, y_hold, stratify=y_hold, random_state=42)
model = LogisticRegression(max_iter=1000).fit(X_train, y_train)
precision, recall, thresholds = precision_recall_curve(y_val, model.predict_proba(X_val)[:, 1])
f1 = 2 * precision[:-1] * recall[:-1] / np.maximum(precision[:-1] + recall[:-1], 1e-12)
threshold = thresholds[f1.argmax()]
scores = model.predict_proba(X_test)[:, 1]
print(threshold, f1_score(y_test, scores >= threshold), average_precision_score(y_test, scores))
@@ scikit-learn | Grouped and chronological validation
Rows from the same customer may be highly correlated. GroupKFold keeps each customer in one side of each split. For forecasting, use ordered observations with TimeSeriesSplit, and choose a gap that accounts for the prediction horizon and label availability.
Create a grouped time-dependent dataset. Describe how you would build a split satisfying both isolation constraints.
What must never overlap in a GroupKFold train/validation pair? | The group IDs | The feature column names | The estimator class
---
import numpy as np
from sklearn.model_selection import GroupKFold, TimeSeriesSplit

X = np.arange(24).reshape(-1, 1)
groups = np.repeat(np.arange(6), 4)
for train, valid in GroupKFold(3).split(X, groups=groups):
    assert set(groups[train]).isdisjoint(groups[valid])
for train, valid in TimeSeriesSplit(n_splits=3, gap=2).split(X):
    assert valid.min() - train.max() > 2
    print(train.max(), valid.min())
@@ scikit-learn | PCA, clustering, and silhouette analysis
PCA rotates centered features into components ordered by explained variance. Scale features when their units would otherwise dominate distances. KMeans minimizes within-cluster squared distances. Silhouette helps compare assignments when labels are unavailable, but does not prove that clusters are meaningful.
Try two through five clusters and compare silhouettes. Fit any reusable transformation on training data when working in a predictive setting.
What does PCA prioritize? | Directions that explain variance | Directions matching known labels | Directions with the highest row counts
---
from sklearn.cluster import KMeans
from sklearn.datasets import make_blobs
from sklearn.decomposition import PCA
from sklearn.metrics import silhouette_score
from sklearn.pipeline import make_pipeline
from sklearn.preprocessing import StandardScaler

X, _ = make_blobs(n_samples=300, centers=3, n_features=4, cluster_std=.7, random_state=42)
Z = make_pipeline(StandardScaler(), PCA(n_components=2)).fit_transform(X)
labels = KMeans(n_clusters=3, n_init=10, random_state=42).fit_predict(Z)
print(Z.shape, round(silhouette_score(Z, labels), 3))
@@ scikit-learn | Incremental training and feature hashing
partial_fit lets supported estimators learn in batches. A HashingVectorizer is stateless and can encode new text without building a vocabulary. Larger hash spaces reduce collisions at the expense of model size. Train in batches and evaluate on text that was kept aside.
Measure validation performance as batches arrive, and compare two hash sizes.
When must the full class list be passed to SGDClassifier.partial_fit? | On the first call | Only after the last call | Each time new text arrives
---
from sklearn.feature_extraction.text import HashingVectorizer
from sklearn.linear_model import SGDClassifier

vectorizer = HashingVectorizer(n_features=2**12, alternate_sign=False)
model = SGDClassifier(loss="log_loss", random_state=42)
for messages, labels in [(["refund order", "password reset"], [0, 1]),
                         (["refund payment", "reset account"], [0, 1])]:
    model.partial_fit(vectorizer.transform(messages), labels, classes=[0, 1])
print(model.predict(vectorizer.transform(["refund purchase", "reset password"])))
@@ scikit-learn | Inspection, persistence, and input contracts
Permutation importance measures how much a held-out score falls after shuffling a feature. Correlated features can share importance. A persisted model needs the same input schema and a compatible library environment. Only load a pickle or joblib file that you created or fully trust.
Record feature order and library versions with the saved pipeline. Demonstrate what happens when an input column is missing.
What does held-out permutation importance measure? | The score change caused by shuffling a feature | Causal effect in the real world | The feature's mean value
---
from pathlib import Path
from tempfile import TemporaryDirectory
import joblib
from sklearn.datasets import load_wine
from sklearn.ensemble import RandomForestClassifier
from sklearn.inspection import permutation_importance
from sklearn.model_selection import train_test_split

X, y = load_wine(return_X_y=True, as_frame=True)
X_train, X_test, y_train, y_test = train_test_split(X, y, stratify=y, random_state=42)
model = RandomForestClassifier(n_estimators=40, random_state=42).fit(X_train, y_train)
result = permutation_importance(model, X_test, y_test, n_repeats=4, random_state=42)
print(X_test.columns[result.importances_mean.argmax()])
with TemporaryDirectory() as directory:
    path = Path(directory) / "local-model.joblib"
    joblib.dump(model, path)
    restored = joblib.load(path)
    assert (model.predict(X_test) == restored.predict(X_test)).all()
@@ PyTorch | Tensors, devices, and NumPy interoperability
A tensor has shape, dtype, and device. Operations generally require inputs on the same device. from_numpy can share CPU memory, so use clone when you need independent storage. A device fallback lets the same lesson run without CUDA.
Change the first NumPy element after creating a shared tensor and a clone. Compare what each tensor sees.
Which property determines where tensor computation happens? | device | dtype | shape
---
import numpy as np
import torch

array = np.array([[1., 2.], [3., 4.]], dtype=np.float32)
shared = torch.from_numpy(array)
independent = shared.clone()
array[0, 0] = 10
assert shared[0, 0].item() == 10 and independent[0, 0].item() == 1
device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
print((independent.to(device) * 2).cpu())
@@ PyTorch | Autograd and gradient accumulation
Autograd records differentiable operations on tensors that require gradients. backward computes gradients, and subsequent backward passes add to existing gradients. Clear them before each independent training step. Use no_grad for calculations that do not need a backward graph.
Derive the derivative of the quadratic on paper and compare it with the printed value.
Why clear gradients between independent steps? | backward adds to existing gradients | backward changes tensor dtype | The optimizer clears all parameters otherwise
---
import torch

weight = torch.tensor(2., requires_grad=True)
loss = (weight * 3 - 12).square()
loss.backward()
assert weight.grad.item() == -36
with torch.no_grad():
    weight -= .05 * weight.grad
weight.grad = None
print(weight.item())
@@ PyTorch | Dataset and DataLoader batching
A dataset supplies samples, while a DataLoader groups them into batches. Fit preprocessing on the training split and shuffle training batches only. Validation order can stay fixed. Use num_workers=0 for the first examples to keep script behavior simple across platforms.
Change the batch size and count the examples consumed. Compare with drop_last=True.
What does DataLoader batch_size control? | The typical number of samples per batch | Number of output classes | Number of training epochs
---
import torch
from torch.utils.data import DataLoader, TensorDataset

features = torch.arange(24, dtype=torch.float32).reshape(12, 2)
labels = (features[:, 0] > 10).long()
loader = DataLoader(TensorDataset(features, labels), batch_size=5, shuffle=True,
                    generator=torch.Generator().manual_seed(42))
sizes = [len(y) for X, y in loader]
assert sizes == [5, 5, 2]
print(sizes)
@@ PyTorch | Modules, logits, and a complete training loop
An nn.Module owns parameters and defines its forward computation. CrossEntropyLoss expects raw logits and integer class indices. Train with zero_grad, forward, backward, and step. Switch to eval and inference_mode for held-out prediction.
Replace AdamW with SGD and compare training curves without looking at test metrics to choose the optimizer.
What should CrossEntropyLoss receive as predictions? | Raw logits | Rounded class IDs | Probabilities after applying softmax
---
import torch
from torch import nn
from torch.utils.data import DataLoader, TensorDataset

torch.manual_seed(42)
X = torch.randn(240, 2)
y = (X[:, 0] + X[:, 1] > 0).long()
loader = DataLoader(TensorDataset(X[:192], y[:192]), batch_size=32, shuffle=True)
model = nn.Sequential(nn.Linear(2, 16), nn.ReLU(), nn.Linear(16, 2))
optimizer = torch.optim.AdamW(model.parameters(), lr=.02)
loss_fn = nn.CrossEntropyLoss()
for epoch in range(12):
    model.train()
    for batch, target in loader:
        optimizer.zero_grad(set_to_none=True)
        loss = loss_fn(model(batch), target)
        loss.backward()
        optimizer.step()
model.eval()
with torch.inference_mode():
    accuracy = (model(X[192:]).argmax(dim=1) == y[192:]).float().mean()
    print(round(accuracy.item(), 3))
    assert accuracy > .8
@@ PyTorch | Checkpoints and inference parity
Save a state_dict to store parameters and buffers. Reconstruct the model architecture and load weights before evaluation. For training resumption also record optimizer, scheduler, epoch, and random state as appropriate. Prefer weights_only for this local tensor checkpoint.
Add an optimizer checkpoint and resume the next training step. Check what changes if the restored model is still in training mode.
What must you recreate before loading these weights? | A compatible model architecture | The entire training dataset | The original GPU driver
---
from pathlib import Path
from tempfile import TemporaryDirectory
import torch
from torch import nn

original = nn.Linear(3, 1).eval()
sample = torch.tensor([[1., 2., 3.]])
with TemporaryDirectory() as directory:
    path = Path(directory) / "weights.pt"
    torch.save(original.state_dict(), path)
    restored = nn.Linear(3, 1)
    restored.load_state_dict(torch.load(path, map_location="cpu", weights_only=True))
    restored.eval()
    with torch.inference_mode():
        torch.testing.assert_close(original(sample), restored(sample))
        print(restored(sample))
@@ PyTorch | Convolution, pooling, and image tensor shapes
Conv2d consumes images in batch, channels, height, width order. A spatial kernel learns local patterns. Adaptive pooling reduces each feature map to a fixed spatial size, and a linear head maps the resulting channel summary to logits.
Change the image resolution and confirm that the logits still have one column per class.
What is dimension one in a standard Conv2d input batch? | Channels | Batch size | Image width
---
import torch
from torch import nn

model = nn.Sequential(nn.Conv2d(1, 8, kernel_size=3, padding=1), nn.ReLU(),
                      nn.AdaptiveAvgPool2d(1), nn.Flatten(), nn.Linear(8, 3))
images = torch.randn(4, 1, 16, 16)
labels = torch.tensor([0, 1, 2, 0])
logits = model(images)
assert logits.shape == (4, 3)
nn.CrossEntropyLoss()(logits, labels).backward()
print(logits.shape)
@@ PyTorch | Attention masks and transformer encoders
With batch_first=True, the encoder takes batch, sequence, and embedding dimensions. A boolean key padding mask marks padding positions with True. Positional information is added explicitly. Pool only valid tokens when reducing a sequence to one vector.
Change only the padded values and compare the pooled results. Then build a separate causal mask for an autoregressive example.
What does True mean in src_key_padding_mask? | Ignore that key as padding | Force that token to attend to itself | Treat that token as the label
---
import torch
from torch import nn

torch.manual_seed(42)
layer = nn.TransformerEncoderLayer(d_model=8, nhead=2, batch_first=True, dropout=0)
encoder = nn.TransformerEncoder(layer, num_layers=2, enable_nested_tensor=False).eval()
tokens = torch.randn(2, 4, 8)
positions = nn.Embedding(4, 8)(torch.arange(4))
padding = torch.tensor([[False, False, False, True], [False, False, True, True]])
with torch.inference_mode():
    output = encoder(tokens + positions, src_key_padding_mask=padding)
    pooled = output.masked_fill(padding[..., None], 0).sum(dim=1) / (~padding).sum(dim=1, keepdim=True)
    assert pooled.shape == (2, 8)
    print(pooled.shape)
@@ PyTorch | Mixed precision, clipping, and optimization
CUDA autocast selects lower precision where appropriate, and a GradScaler helps protect small gradients from underflow. Unscale before gradient clipping so the threshold operates on the original magnitude. The CPU fallback uses ordinary precision and still performs the same training step.
Run on CPU, then compare CUDA throughput and numerical agreement when compatible hardware is available.
When should gradients be unscaled relative to clipping? | Before clipping | After clipping | Only after calling optimizer.step()
---
import torch
from torch import nn

device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
amp = device.type == "cuda"
model = nn.Linear(8, 1).to(device)
optimizer = torch.optim.AdamW(model.parameters(), lr=.001)
scaler = torch.amp.GradScaler(device.type, enabled=amp)
X = torch.randn(32, 8, device=device)
y = torch.randn(32, 1, device=device)
optimizer.zero_grad(set_to_none=True)
with torch.autocast(device_type=device.type, enabled=amp):
    loss = nn.functional.mse_loss(model(X), y)
scaler.scale(loss).backward()
scaler.unscale_(optimizer)
torch.nn.utils.clip_grad_norm_(model.parameters(), 1.0)
scaler.step(optimizer)
scaler.update()
print(device, loss.item())
@@ PyTorch | Profiling and bottleneck analysis
Profile before optimizing. The profiler attributes CPU operator time and can track device activity on supported hardware. Warm up first and use a representative workload. Benchmark eager versus torch.compile only after checking output parity and excluding initial compilation time.
Change input size, then compare operator time. Explain why profiling overhead and compilation warmup affect short measurements.
Why exclude the initial torch.compile call from steady-state latency? | It may include graph compilation and warmup | It always runs on another machine | It removes the model's parameters
---
import torch
from torch import nn
from torch.profiler import ProfilerActivity, profile

torch.set_num_threads(2)
model = nn.Sequential(nn.Linear(128, 256), nn.ReLU(), nn.Linear(256, 16)).eval()
X = torch.randn(128, 128)
with torch.inference_mode():
    model(X)
    with profile(activities=[ProfilerActivity.CPU], record_shapes=True) as trace:
        for _ in range(5):
            model(X)
print(trace.key_averages().table(sort_by="self_cpu_time_total", row_limit=5))
@@ PyTorch | DistributedDataParallel on a CPU
DistributedDataParallel synchronizes model gradients across processes. Use a DistributedSampler to split training examples and call set_epoch when shuffling in a multi-epoch loop. This single-machine lab uses two spawned processes and a temporary rendezvous file. Run this lab in Linux or WSL with a PyTorch build that supports the Gloo CPU backend. Install packages inside that environment before running the example. For multi-machine training, use torchrun with a shared rendezvous endpoint and a backend appropriate for your devices.
Run two ranks and inspect which rows each rank receives. Add more epochs and confirm that both ranks end with matching weights.
What does DistributedDataParallel synchronize by default during backward? | Gradients across participating ranks | All input rows onto one worker | Python source files
---
import sys

import torch
import torch.distributed as dist
import torch.multiprocessing as mp
from pathlib import Path
from tempfile import TemporaryDirectory
from torch import nn
from torch.nn.parallel import DistributedDataParallel
from torch.utils.data import DataLoader, DistributedSampler, TensorDataset

def train(rank, rendezvous):
    dist.init_process_group("gloo", init_method=rendezvous, rank=rank, world_size=2)
    try:
        torch.manual_seed(42)
        X = torch.arange(16, dtype=torch.float32).reshape(-1, 1) / 16
        data = TensorDataset(X, 2 * X)
        sampler = DistributedSampler(data)
        sampler.set_epoch(0)
        model = DistributedDataParallel(nn.Linear(1, 1))
        optimizer = torch.optim.SGD(model.parameters(), lr=.1)
        for features, target in DataLoader(data, batch_size=4, sampler=sampler):
            optimizer.zero_grad(set_to_none=True)
            nn.functional.mse_loss(model(features), target).backward()
            optimizer.step()
        print(dist.get_rank(), model.module.weight.item())
    finally:
        dist.destroy_process_group()

if __name__ == "__main__":
    if sys.platform == "win32" or not dist.is_available() or not dist.is_gloo_available():
        raise SystemExit("This lab requires a Linux or WSL PyTorch build with Gloo.")
    with TemporaryDirectory() as directory:
        rendezvous = (Path(directory) / "rendezvous").as_uri()
        mp.spawn(train, args=(rendezvous,), nprocs=2, join=True)
@@ Capstone | Compare classical and neural models on the same data
Start with a reproducible numeric dataset, inspect it in pandas, and build a classical baseline. Fit scaling on the training split only. Train a small neural model on the same transformed input, then compare test accuracy. In a real project, reserve validation data for model decisions and publish split details, feature contracts, and limitations with the results.
Add a validation split, report balanced accuracy and a confusion matrix, and compare model latency using the same batch size. Train and save the selected model only after the validation protocol is decided.
Why use the same test rows for both models? | It makes their comparison hold evaluation data fixed | It gives both identical learned parameters | It guarantees the neural network wins
---
import numpy as np
import pandas as pd
import torch
from torch import nn
from sklearn.linear_model import LogisticRegression
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler

rng = np.random.default_rng(42)
torch.manual_seed(42)
columns = ["activity", "tenure", "volume"]
X = pd.DataFrame(rng.normal(size=(500, 3)), columns=columns)
y = (X.activity + .6 * X.tenure - .3 * X.volume > 0).astype(int)
X_train, X_test, y_train, y_test = train_test_split(X, y, stratify=y, random_state=42)
scaler = StandardScaler().fit(X_train)
train = scaler.transform(X_train)
test = scaler.transform(X_test)
baseline = LogisticRegression().fit(train, y_train)
network = nn.Sequential(nn.Linear(3, 12), nn.ReLU(), nn.Linear(12, 2))
optimizer = torch.optim.AdamW(network.parameters(), lr=.03)
features = torch.tensor(train, dtype=torch.float32)
target = torch.tensor(y_train.to_numpy(), dtype=torch.long)
for _ in range(35):
    optimizer.zero_grad(set_to_none=True)
    nn.functional.cross_entropy(network(features), target).backward()
    optimizer.step()
network.eval()
with torch.inference_mode():
    prediction = network(torch.tensor(test, dtype=torch.float32)).argmax(1).numpy()
print({"logistic": baseline.score(test, y_test), "neural": float(np.mean(prediction == y_test.to_numpy()))})
CURRICULUM;
