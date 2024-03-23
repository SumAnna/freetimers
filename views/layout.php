<!DOCTYPE html>
<html lang="en">
<head>
    <title>Soil calculator</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Bentham|Playfair+Display|Raleway:400,500|Suranna|Trocchi" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../public/css/main.css">
</head>
<body>
    <div class="container">
        <div class="row p-3">
            <div class="shopping-cart">
                <i class="fa fa-shopping-cart"></i>
                <span class="cart-quantity"><?php echo $basketCounter ?? 0; ?></span>
            </div>

            <div class="product-info col-9 col-s-12 rounded shadow p-4 border border-2">
                <div class="product-text">
                    <h1>Top Soil Calculator</h1>
                    <h5 class="mb-3">How many bags you need?</h5>
                </div>

                <div class="intro-text p-3 mb-2 rounded">
                    Our formula for calculations is: <b>(Length * Width) * 0.025 * Depth</b>.<br>We round the result to the next integer.
                </div>

                <div class="d-none errors-section p-3 mb-2 rounded">
                    <p class="error" id="errorTexts"></p>
                </div>

                <form method="POST" id="calculatorForm">
                    <label for="unit" class="select-label">Choose measurements unit</label>
                    <select class="form-select mb-2" id="unit" name="unit" required>
                        <?php foreach ($unitsMeasurement ?? [] as $um) { ?>
                            <option value="<?php echo ($um['unit_id'] ?? 0); ?>"><?php echo ($um['unit_name'] ?? '') . ' (' . ($um['unit_short_name'] ?? '') . ')'; ?></option>
                        <?php } ?>
                    </select>

                    <label for="unitDepth" class="select-label">Choose depth unit</label>
                    <select class="form-select mb-2" id="unitDepth" name="unitDepth" required>
                        <?php foreach ($unitsDepth ?? [] as $ud) { ?>
                            <option value="<?php echo ($ud['unit_id'] ?? 0); ?>"><?php echo ($ud['unit_name'] ?? '') . ' (' . ($ud['unit_short_name'] ?? '') . ')'; ?></option>
                        <?php } ?>
                    </select>

                    <div class="input-group mt-4">
                        <span class="input-group-text">Length</span>
                        <input type="number" class="form-control" placeholder="Insert length value" min="0" name="length" id="length" required step="any">
                    </div>

                    <div class="input-group mt-4">
                        <span class="input-group-text">Width</span>
                        <input type="number" class="form-control" placeholder="Insert width value" min="0" name="width" id="width" required step="any">
                    </div>

                    <div class="input-group mt-4">
                        <span class="input-group-text">Depth</span>
                        <input type="number" class="form-control" placeholder="Insert depth value" min="0" name="depth" id="depth" required step="any">
                    </div>

                    <input type="hidden" value="<?php echo $csrf ?? ''; ?>" name="csrf-token" id="csrf-token">

                    <div class="d-grid gap-2 mt-4">
                        <button class="btn submit-button" type="submit">Calculate</button>
                    </div>
                </form>

                <form method="POST" id="basketForm" class="d-none">
                    <div class="result-section">
                        <hr class="mt-5">
                        <p>You will need <span class="result" id="bagsNumber"></span> bulk bags of topsoil.</p>
                        <p>This will cost you <span class="result">£</span><span class="result" id="bagsPrice"></span>.</p>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button class="btn submit-button" type="submit" form="basketForm">Add To Basket</button>
                    </div>

                    <input type="hidden" value="<?php echo $csrfBasket ?? ''; ?>" name="basket-csrf-token" id="basket-csrf-token">
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="../public/js/main.js"></script>
</body>
</html>