$(document).ready(function() {
    // Sidebar toggle functionality
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("sb-sidenav-toggled");
    }); // <-- Corrected: Added closing brace and parenthesis.

    // Navigation link functionality
    $('.nav-link').click(function(e) {
        e.preventDefault();
        var target = $(this).data('target');
        $('.content-section').hide();
        $('#' + target).fadeIn(300);

        // Highlight active link
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
    });

    // Set initial active link
    $('.nav-link[data-target="dashboard"]').addClass('active');

    // --- Chart.js ---
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['April', 'May', 'June', 'July', 'August', 'September'],
            datasets: [{
                label: 'Sales (₹)',
                data: [12000, 19000, 9000, 17000, 11000, 22000],
                backgroundColor: 'rgba(0, 123, 255, 0.6)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Your new code for coupon management
    document.addEventListener('DOMContentLoaded', function() {
        const generateBtn = document.querySelector('.input-group .btn-outline-secondary');
        const couponCodeInput = document.getElementById('couponCode');
        const saveCouponBtn = document.querySelector('button[type="submit"]');

        if (generateBtn) {
            generateBtn.addEventListener('click', function() {
                const length = 8;
                const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let generatedCode = '';
                for (let i = 0; i < length; i++) {
                    generatedCode += characters.charAt(Math.floor(Math.random() * characters.length));
                }
                couponCodeInput.value = generatedCode;
            });
        }

        if (saveCouponBtn) {
            saveCouponBtn.addEventListener('click', function(event) {
                event.preventDefault(); 
                const couponCode = document.getElementById('couponCode').value;
                const discountType = document.querySelector('select').value;
                const discountValue = document.getElementById('discountValue').value;
                const expiryDate = document.getElementById('expiryDate').value;
                const usageLimit = document.getElementById('usageLimitCheck').checked;

                if (!couponCode || !discountValue || !expiryDate) {
                    alert('Please fill in all required fields.');
                    return;
                }
                
                console.log({
                    code: couponCode,
                    type: discountType,
                    value: discountValue,
                    expiry: expiryDate,
                    limitedUse: usageLimit
                });
                
                alert('Coupon saved successfully!');
            });
        }
    });
}); 