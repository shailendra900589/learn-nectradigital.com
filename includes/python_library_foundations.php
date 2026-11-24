<?php
declare(strict_types=1);

return <<<'CURRICULUM'
@@ Setup | Python library setup and virtual environments
A virtual environment gives this course its own interpreter and package directory. Run the PowerShell commands from an empty project folder. On macOS or Linux, use python3 -m venv .venv and replace .\.venv\Scripts\python.exe with .venv/bin/python. Use Python 3.12 or 3.13 for broad wheel compatibility. The PyTorch command below selects Windows CPU wheels. On macOS, use .venv/bin/python -m pip install torch. For Linux or a GPU build, select the platform in the official PyTorch installer. Save each Python example as lesson.py and run it with the same interpreter.
Check that pip and the imported packages belong to the virtual environment. Keep the generated requirements.txt to reproduce this environment on a compatible platform.
Why invoke pip through the environment's Python? | It installs into the interpreter that runs the lessons | It downloads a new Python interpreter | It automatically installs GPU drivers
---
python -m venv .venv
.\.venv\Scripts\python.exe -m pip install --upgrade pip
.\.venv\Scripts\python.exe -m pip install "numpy>=2" "pandas>=3" "scikit-learn>=1.6" pyarrow
.\.venv\Scripts\python.exe -m pip install torch --index-url https://download.pytorch.org/whl/cpu
.\.venv\Scripts\python.exe -m pip check
.\.venv\Scripts\python.exe -c "import numpy, pandas, sklearn, torch; print(numpy.__version__, pandas.__version__, sklearn.__version__, torch.__version__)"
.\.venv\Scripts\python.exe -m pip freeze > requirements.txt
@@ NumPy | Arrays, shape, and dtypes
An ndarray stores values of a single dtype. Shape describes the length of each axis. Start with an integer matrix, then cast to float before division. Itemsize tells you how many bytes each value occupies.
Add another row and predict the new shape and nbytes before running the script.
Which property describes the lengths of the axes? | shape | dtype | itemsize
---
import numpy as np

sales = np.array([[2, 4, 6], [3, 6, 9]], dtype=np.int32)
assert sales.shape == (2, 3)
assert sales.nbytes == 24
print(sales.shape, sales.dtype, sales.nbytes)
print(sales.astype(np.float64) / 2)
@@ NumPy | Indexing, masks, and safe copies
Basic slices often share storage. Boolean and integer-array indexing produce copies, so modifying the intermediate array does not update the original. Assign into the original with a mask when you want an in-place update.
Use a mask to replace all values over 30 with zero. Confirm that changing an explicit copy leaves the source unchanged.
What separates the slice from the source storage? | Calling copy() | Taking another basic slice | Changing the variable name
---
import numpy as np

values = np.arange(10, 60, 10)
view = values[1:3]
view[0] = 99
assert values[1] == 99
selected = values[values > 30]
selected[:] = 0
assert values[1] == 99
print(values, np.shares_memory(values, view))
@@ NumPy | Broadcasting, axes, and reductions
Broadcasting compares shapes from the trailing dimension. Equal lengths and length one are compatible. A column-shaped array retains its row axis and can combine with a row of feature offsets. Use keepdims when the reduced result must broadcast back.
Center each column independently and verify that the centered column means are zero.
Why use keepdims=True for a row mean? | It retains a length-one column axis for broadcasting | It turns the array into a list | It reduces memory precision
---
import numpy as np

scores = np.array([[2., 4., 6.], [10., 20., 30.]])
row_means = scores.mean(axis=1, keepdims=True)
centered = scores - row_means
np.testing.assert_allclose(centered.mean(axis=1), 0)
print(centered)
@@ NumPy | Reshape, stack, and concatenate
Reshape changes the arrangement of the same number of elements. Stack creates an axis, while concatenate extends an existing one. Check shapes before passing data between libraries and use an explicit copy when independent storage matters.
Combine two three-row batches into six rows and compare the result with stacking them on a new batch axis.
Which operation introduces a new axis? | np.stack | np.concatenate | np.sort
---
import numpy as np

first = np.arange(6).reshape(3, 2)
second = first + 10
assert np.stack([first, second]).shape == (2, 3, 2)
assert np.concatenate([first, second]).shape == (6, 2)
print(first.T, first.flags.c_contiguous)
@@ NumPy | Random generators and bootstrap confidence intervals
A seeded Generator makes this simulation repeatable in the same environment. Bootstrap resampling draws observations with replacement, then builds a distribution of the statistic. This simple percentile interval assumes independent, representative observations.
Compute a bootstrap interval for the median. Record the seed and compare results using a larger sample.
What makes bootstrap samples contain duplicate observations? | Sampling with replacement | Sorting before sampling | Drawing each item once
---
import numpy as np

rng = np.random.default_rng(42)
latency = np.array([24, 27, 30, 31, 33, 40, 45, 50])
samples = rng.choice(latency, size=(2000, latency.size), replace=True)
interval = np.quantile(samples.mean(axis=1), [.025, .975])
assert interval[0] < latency.mean() < interval[1]
print(interval.round(2))
@@ NumPy | Linear algebra and least squares
Matrix multiplication combines rows with columns. Use lstsq for an overdetermined system instead of explicitly computing an inverse. Inspect rank before interpreting coefficients, since a rank-deficient design can admit multiple solutions.
Add a redundant feature and inspect how rank changes. Explain why a good fit no longer guarantees unique coefficients.
Which function fits an overdetermined linear system? | np.linalg.lstsq | np.linalg.inv | np.sort
---
import numpy as np

days = np.arange(4.)
design = np.column_stack((np.ones(days.size), days))
sales = 10 + 3 * days
coef, residuals, rank, singular_values = np.linalg.lstsq(design, sales, rcond=None)
assert rank == 2
np.testing.assert_allclose(coef, [10, 3])
print(design @ coef)
@@ NumPy | Numerical stability and masked division
Floating-point arithmetic is approximate. Stabilize exponentials by shifting inputs, then restore the shift after taking the logarithm. Use initialized output with ufunc where masks, because excluded positions are left untouched.
Add a third logit to the example and check the result with np.logaddexp.reduce.
Why subtract the maximum before exponentiating? | To avoid overflow in large positive inputs | To make all probabilities zero | To sort the logits
---
import numpy as np

logits = np.array([1000., 1001.])
offset = logits.max()
result = offset + np.log(np.exp(logits - offset).sum())
np.testing.assert_allclose(result, np.logaddexp.reduce(logits))
denominator = np.array([2., 0.])
ratio = np.divide([6., 4.], denominator, out=np.full(2, np.nan), where=denominator != 0)
print(result, ratio)
@@ NumPy | FFT and signal frequency
The FFT decomposes equally spaced samples into frequencies. rfft keeps the nonnegative half for real input. rfftfreq uses the sample spacing to express bins in hertz. This signal spans whole cycles, so its strongest component falls on an exact bin.
Add a quieter 12 Hz signal. Identify both peaks and explain what changes when the sampled interval no longer spans whole cycles.
What does d=1/sample_rate represent? | The time between samples | Signal amplitude | The number of harmonics
---
import numpy as np

sample_rate = 128
t = np.arange(sample_rate) / sample_rate
signal = np.sin(2 * np.pi * 8 * t)
frequencies = np.fft.rfftfreq(t.size, d=1 / sample_rate)
spectrum = np.abs(np.fft.rfft(signal))
peak = frequencies[spectrum[1:].argmax() + 1]
assert peak == 8
print(peak)
@@ NumPy | Memory mapping and chunked reductions
A memory-mapped .npy file lets you read slices without eagerly loading the whole array. Process bounded blocks and combine their sums and counts. Small Python loops over chunks are reasonable, while inner numeric work stays vectorized.
Calculate minimum and maximum in the same pass and compare both with an in-memory calculation.
Why process the file in chunks? | To bound the working memory used by each operation | To encrypt the file | To avoid all disk reads
---
from pathlib import Path
from tempfile import TemporaryDirectory
import numpy as np

with TemporaryDirectory() as directory:
    path = Path(directory) / "values.npy"
    np.save(path, np.arange(1000, dtype=np.float64))
    mapped = np.load(path, mmap_mode="r", allow_pickle=False)
    total = sum(mapped[i:i + 128].sum() for i in range(0, mapped.size, 128))
    assert total / mapped.size == 499.5
    print(total / mapped.size)
    del mapped
@@ pandas | Series, DataFrames, and label alignment
A Series attaches labels to a single column. A DataFrame combines labelled columns sharing an index. Arithmetic between Series aligns by index label, even when the second Series has a different row order.
Add a mismatched label and use isna() to inspect the gap before deciding whether to fill it.
How does Series arithmetic match rows? | By index label | By the current display order only | By dtype
---
import pandas as pd

orders = pd.DataFrame({"price": [10, 25]}, index=["A", "B"])
quantity = pd.Series([3, 2], index=["B", "A"])
orders["total"] = orders["price"] * quantity
assert orders["total"].tolist() == [20, 75]
print(orders)
@@ pandas | CSV parsing, missing data, and nullable dtypes
Choose dtypes while reading data to protect identifiers and preserve missing values. Nullable Int64 can store an unknown quantity without converting the entire column to floats. Decide how missing values should be handled based on what the field means.
Add a row with a missing identifier, then exclude it before aggregating quantities.
Why load SKU codes as strings? | To preserve leading zeros | To round the codes | To automatically remove duplicates
---
from io import StringIO
import pandas as pd

csv = StringIO("sku,qty\n001,2\n002,\n003,6\n")
frame = pd.read_csv(csv, dtype={"sku": "string", "qty": "Int64"})
assert frame.loc[0, "sku"] == "001"
assert frame["qty"].isna().sum() == 1
print(frame.dropna(subset=["qty"]))
@@ pandas | Filtering, .loc, and Copy-on-Write
Use .loc with a row mask and a column selector to update the original DataFrame. pandas 3 uses Copy-on-Write, so chained assignment does not reliably express an update to the original. Use .iloc when you specifically need integer positions.
Use .loc to flag orders with quantity below two. Check that a separately derived frame cannot mutate its parent.
What performs a single assignment into the original frame? | frame.loc[mask, "status"] = "large" | frame[mask]["status"] = "large" | frame.iloc[mask]["status"]
---
import pandas as pd

frame = pd.DataFrame({"qty": [1, 5, 3], "status": ["small"] * 3})
snapshot = frame[["status"]]
frame.loc[frame["qty"] >= 3, "status"] = "large"
assert frame["status"].tolist() == ["small", "large", "large"]
assert snapshot["status"].tolist() == ["small"] * 3
print(frame)
@@ pandas | GroupBy aggregation and transform
Aggregation reduces a group to a summary row. Transform broadcasts a group statistic back to the input rows, which makes it useful for within-group shares or centering. Explicitly handle zero denominators in real datasets.
Compute each order's share of its region's total and check that shares add up to one.
Which operation returns a group statistic aligned with source rows? | transform | agg | drop_duplicates
---
import pandas as pd

orders = pd.DataFrame({"region": ["E", "W", "E"], "amount": [20, 60, 30]})
groups = orders.groupby("region")["amount"]
print(groups.agg(total="sum", average="mean"))
orders["share"] = orders["amount"] / groups.transform("sum")
assert orders.loc[0, "share"] == .4
print(orders)
@@ pandas | Joins, validated keys, and reshaping
A left merge retains order rows. Validate the expected key cardinality to catch accidental multiplication, and inspect unmatched keys using the merge indicator. Pivot tables summarize repeated observations when reshaping long data into columns.
Add a duplicate key to the lookup table and observe the exception before fixing the lookup.
What does many_to_one require? | Unique keys in the right-hand table | Unique keys in both tables | Every left key must match
---
import pandas as pd

orders = pd.DataFrame({"sku": ["A", "A", "B"], "month": [1, 2, 1], "units": [2, 3, 4]})
catalog = pd.DataFrame({"sku": ["A", "B"], "category": ["books", "tools"]})
joined = orders.merge(catalog, on="sku", how="left", validate="many_to_one", indicator=True)
assert joined["_merge"].eq("both").all()
wide = joined.pivot_table(index="category", columns="month", values="units", aggfunc="sum", fill_value=0)
print(wide)
@@ pandas | Time zones, resampling, and rolling windows
Use timezone-aware timestamps and sort by time before time-series operations. Resample puts observations into fixed time bins. Rolling windows summarize recent observations and may overlap. Always define the timezone that gives a business day its meaning.
Convert to Asia/Kolkata before resampling and explain how timestamps near midnight move between calendar dates.
What does rolling(2) mean in this script? | Two rows in each rolling window | Two calendar weeks | Two TimeZones
---
import pandas as pd

times = pd.to_datetime(["2026-01-01 23:00", "2026-01-02 01:00", "2026-01-03 03:00"], utc=True)
events = pd.Series([2, 6, 4], index=times).sort_index()
daily = events.resample("D").sum()
assert daily.tolist() == [2, 6, 4]
print(daily.rolling(2, min_periods=1).mean())
@@ pandas | Parquet, categories, and chunked ingestion
Repeated low-cardinality strings can be represented as categories. Read only required columns from Parquet and use a compatible engine such as PyArrow. With CSV chunks, combine partial sufficient statistics, such as totals and counts, rather than averaging chunk averages.
Read category and amount from a larger export, then compare memory_usage(deep=True) before and after converting the category column.
Why combine totals and counts instead of chunk means? | Chunks can contain different numbers of rows | Parquet requires all columns to be numeric | groupby only works on CSV
---
from io import StringIO
from pathlib import Path
from tempfile import TemporaryDirectory
import pandas as pd

csv = StringIO("group,amount\nA,1\nA,3\nB,6\n")
totals = [chunk.groupby("group").amount.sum() for chunk in pd.read_csv(csv, chunksize=2)]
summary = pd.concat(totals).groupby(level=0).sum().rename("total").reset_index()
summary["group"] = summary["group"].astype("category")
with TemporaryDirectory() as directory:
    path = Path(directory) / "summary.parquet"
    summary.to_parquet(path, index=False)
    reloaded = pd.read_parquet(path, columns=["total"])
    assert reloaded.total.tolist() == [4, 6]
    print(reloaded)
@@ pandas | Project: auditable order reporting
Build a repeatable reporting function that validates input keys, joins orders to product prices, and aggregates paid revenue. Check totals against known examples. In production, use integer cents or an agreed decimal contract for money.
Extend the function to report refunds separately and write a test for unknown product codes.
Why does the function reject a missing unit price? | Otherwise the resulting revenue can silently omit orders | Missing prices break Python imports | Every SKU must contain a number
---
import pandas as pd

def revenue_report(orders, products):
    merged = orders.merge(products, on="sku", how="left", validate="many_to_one")
    if merged["price_cents"].isna().any():
        raise ValueError("Every order needs a catalog price")
    paid = merged.loc[merged["paid"]].copy()
    paid["revenue_cents"] = paid["units"] * paid["price_cents"]
    return paid.groupby("sku")["revenue_cents"].sum()

products = pd.DataFrame({"sku": ["A", "B"], "price_cents": [1099, 750]})
orders = pd.DataFrame({"sku": ["A", "A", "B"], "units": [2, 1, 4], "paid": [True, False, True]})
result = revenue_report(orders, products)
assert result.to_dict() == {"A": 2198, "B": 3000}
print(result)
CURRICULUM;
