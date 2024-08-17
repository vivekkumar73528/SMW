document.getElementById('zip_code').addEventListener('input', function() {
    const zipCode = this.value;
    const zipError = document.getElementById('zip_error');

    if (zipCode.length === 6) { // Assuming Indian zip codes have 6 digits
        fetch(`https://api.postalpincode.in/pincode/${zipCode}`)
            .then(response => response.json())
            .then(data => {
                if (data && data[0].Status === "Success" && data[0].PostOffice) {
                    const postOffice = data[0].PostOffice[0]; // Assuming the first post office is the most relevant
                    document.getElementById('city').value = postOffice.District;
                    document.getElementById('state').value = postOffice.State;
                    zipError.textContent = ''; // Clear error message
                } else {
                    zipError.textContent = 'Invalid Zip code ("Not found")'; // Display error message
                    document.getElementById('city').value = '';
                    document.getElementById('state').value = '';
                }
            })
            .catch(error => {
                console.error('Error fetching location data:', error);
                zipError.textContent = 'Error fetching location data'; // Display error message
                document.getElementById('city').value = '';
                document.getElementById('state').value = '';
            });
    } else {
        zipError.textContent = ''; // Clear error message
        // Clear city and state fields if zip code is less than 6 digits
        document.getElementById('city').value = '';
        document.getElementById('state').value = '';
    }
});



// function updateTotalPrice() {
//     let codCharge = 79;
//     let totalPrice = <?php echo $totalPrice; ?>;
//     let paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
//     if (paymentMethod === 'cod') {
//         totalPrice += codCharge;
//     }
//     document.getElementById('total-price').textContent = '$' + totalPrice.toFixed(2);
// }

// function handleSubmit(event) {
//     let paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
//     if (paymentMethod === 'online') {
//         event.preventDefault();

//         var options = {
//             "key": "YOUR_RAZORPAY_KEY", 
//             "amount": <?php echo $totalPrice * 100; ?>, 
//             "name": "Your Company Name",
//             "description": "Test Transaction",
//             "handler": function(response) {
               
//                 var form = document.createElement("form");
//                 form.method = "POST";
//                 form.action = "order_confirmation.php";

//                 var input1 = document.createElement("input");
//                 input1.type = "hidden";
//                 input1.name = "razorpay_payment_id";
//                 input1.value = response.razorpay_payment_id;
//                 form.appendChild(input1);

//                 var input2 = document.createElement("input");
//                 input2.type = "hidden";
//                 input2.name = "razorpay_order_id";
//                 input2.value = response.razorpay_order_id;
//                 form.appendChild(input2);

//                 var input3 = document.createElement("input");
//                 input3.type = "hidden";
//                 input3.name = "razorpay_signature";
//                 input3.value = response.razorpay_signature;
//                 form.appendChild(input3);

//                 document.body.appendChild(form);
//                 form.submit();
//             },
//             "prefill": {
//                 "name": "",
//                 "email": "",
//                 "contact": ""
//             },
//             "theme": {
//                 "color": "#3399cc"
//             }
//         };

//         var rzp1 = new Razorpay(options);
//         rzp1.open();
//     }
// }
