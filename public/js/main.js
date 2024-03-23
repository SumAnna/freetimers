$(document).ready(function () {
    var basketForm = $('#basketForm');

    $('#calculatorForm').submit(function (event) {
        let errorsSection = $('.errors-section');
        let resultSection = $('.result-section');
        let errorText = $('#errorTexts');
        let bagsText = $('#bagsNumber');
        let bagsPrice = $('#bagsPrice');
        errorsSection.addClass('d-none').removeClass('d-block');
        resultSection.addClass('d-none').removeClass('d-block');
        basketForm.addClass('d-none').removeClass('d-block');
        errorText.text('');
        bagsText.text('');
        bagsPrice.text('');

        let formData = {
            depth: $("#depth").val() ?? 0,
            length: $("#length").val() ?? 0,
            width: $("#width").val() ?? 0,
            unit: $("#unit").val() ?? '',
            unitDepth: $("#unitDepth").val() ?? '',
            csrf: $('#csrf-token').val() ?? ''
        };

        $.ajax({
            type: 'POST',
            url: '/post.php',
            data: formData,
            dataType: "json",
            encode: true
        }).done(function (data) {
            if (data.hasOwnProperty('errors')) {
                errorsSection.addClass('d-block').removeClass('d-none');
                data.errors.forEach(function(error) {
                    errorText.append(error + '<br/>');
                });
            } else if (data.hasOwnProperty('bags') && data.hasOwnProperty('price')) {
                resultSection.addClass('d-block').removeClass('d-none');
                basketForm.addClass('d-block').removeClass('d-none');
                bagsText.text(data.bags);
                bagsPrice.text(data.price);
            } else {
                $('.errors-section').addClass('d-block').removeClass('d-none');
                errorText.text('Unknown error.');
            }
        });

        event.preventDefault();
    });

    basketForm.submit(function (event) {
        let errorsSection = $('.errors-section');
        let errorText = $('#errorTexts');
        let bagsText = $('#bagsNumber');
        errorsSection.addClass('d-none').removeClass('d-block');
        errorText.text('');

        let formData = {
            bagsNumber: bagsText.text() ?? 0,
            csrf: $('#basket-csrf-token').val() ?? ''
        };

        $.ajax({
            type: 'POST',
            url: '/basket.php',
            data: formData,
            dataType: "json",
            encode: true
        }).done(function (data) {
            if (data.hasOwnProperty('errors')) {
                errorsSection.addClass('d-block').removeClass('d-none');
                data.errors.forEach(function(error) {
                    errorText.append(error + '<br/>');
                });
            } else if (data.hasOwnProperty('newBasket')) {
                alert((bagsText.text() ?? 0) + ' soil bag(s) added to your basket.');
                $('.cart-quantity').text(data.newBasket);
            }
        });

        event.preventDefault();
    });
});