<?php
    require_once 'backend/sdbh.php';
    $dbh = new sdbh();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Calculator</title>

        <!-- styles -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <link href="assets/css/style.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <link href="assets/css/style_form.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">

        <style>
            .container{
                margin-top: 50px;
            }
            .col-3{
                background-color: #FF9A00;
                border-radius: 0;
                border-top-left-radius: 13px;
                border-bottom-left-radius: 13px;
                display: flex;
                align-items: center;
                flex-flow: column;
                justify-content: center;
                font-size: 26px;
                font-weight: 900;
            }
            label:not([class="form-check-label"]) {
                font-size: 16px;
                font-weight: 600;
            }
            .form-check-input:checked{
                background-color: #FF9A00;
                border-color: #FF9A00;
            }
            .col-9{
                padding: 25px;
            }
            .row-body {
                border: 3px solid #333;
                border-radius: 15px;
            }
            .btn-primary {
                color: #fff;
                background-color: #FF9A00;
                border-color: #FF9A00;
            }
        </style>
        <!-- end styles -->

        <!-- scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <!-- end scripts -->
    </head>

    <body>
        <div class="container">
            <div class="row row-header">
                <div class="col-12">
                    <img src="assets/img/logo.png" alt="logo" style="max-height:50px"/>
                    <h1>Прокат</h1>
                </div>
            </div>
            
            <div class="row row-body">
                <div class="col-3">
                    <span style="text-align: center">Форма расчета</span>
                    <i class="bi bi-activity"></i>
                </div>

                <div class="col-9">
                    <form id="form">
                        <label class="form-label" for="product">Выберите продукт:</label>
                        <?php
                            $products = $dbh->mselect_rows('a25_products', null, 0, 1000, 'ID');
                        ?>
                        <select class="form-select" name="product" id="product" required>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= $product['ID'] ?>"><?= $product['NAME'] ?> за <?= $product['PRICE'] ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label for="customRange1" class="form-label">Количество дней:</label>
                        <input type="number" class="form-control" id="customRange1" min="1" name="days" value="1" required>

                        <label for="customRange1" class="form-label">Дополнительно:</label>
                        <?php
                            $services = unserialize($dbh->mselect_rows('a25_settings', ['set_key' => 'services'], 0, 1, 'id')[0]['set_value']);
                        ?>
                        <?php foreach ($services as $k => $s): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="<?= $s ?>" id="service_<?= $k ?>" name="services[]">
                                <label class="form-check-label" for="service_<?= $k ?>">
                                    <?= $k ?> за <?= $s ?>
                                </label>
                            </div>
                        <?php endforeach; ?>

                        <button type="submit" class="btn btn-primary">Рассчитать</button>
                    </form>

                    <div id="result"></div>
                </div>
            </div>
        </div>
        
        <script>
            $(document).ready(function() {
                $('#form').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: 'calculate.php',
                        method: 'POST',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function(response) {
                            $('#result').html('<h4>Итоговая стоимость: ' + response.total + '</h4>');
                        },
                        error: function(xhr, status, error) {
                            console.log(error);
                        }
                    });
                });
            });
        </script>
    </body>
</html>