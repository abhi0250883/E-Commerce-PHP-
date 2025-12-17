 $(document).ready(function() {
            // Sidebar toggle functionality
            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("#wrapper").toggleClass("sb-sidenav-toggled");
            });

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

            // --- Discount Code Generator ---
            $('#generate-discount-btn').click(function() {
                var percentage = $('#discount-percentage').val();
                if (percentage > 0 && percentage <= 100) {
                    var length = 8;
                    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    var resultCode = '';
                    for (var i = 0; i < length; i++) {
                        resultCode += characters.charAt(Math.floor(Math.random() * characters.length));
                    }
                    var displayText = 'Your code: <strong>' + resultCode + '</strong><br>Grants a <strong>' + percentage + '%</strong> discount!';
                    $('#discount-card-display').html(displayText).fadeIn();
                } else {
                    alert('Please enter a discount percentage between 1 and 100.');
                }
            });

            // --- Brand Form Submission Logic ---
            $('#brandForm').submit(function(event) {
                event.preventDefault();
                const brandName = $('#brandNameInput').val().trim();
                const messageElement = $('#message');

                if (brandName) {
                    console.log('Brand Name Saved:', brandName);
                    messageElement.text(`✅ Brand "${brandName}" was saved successfully!`);
                    $('#brandNameInput').val(''); // Clear the input field

                    setTimeout(() => {
                        messageElement.text('');
                    }, 4000);
                } else {
                    alert('Please enter a brand name.');
                }
            });
        });