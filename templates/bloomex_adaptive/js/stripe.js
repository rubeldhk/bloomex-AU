// This is your test publishable API key.

//    const stripe = Stripe("REDACTED_STRIPE_PUBLISHABLE");

let elements;
let payment_intend;



// Fetches a payment intent and captures the client secret
async function initializeStripePayment(clientSecret) {
    payment_intend = clientSecret;
    elements = stripe.elements({ clientSecret });

    const paymentElementOptions = {
        layout: "tabs",
        fields: {
            billingDetails: {
                address: {
                    country: 'never',
                    postalCode: 'never'
                }
            }
        }
    };

    const paymentElement = elements.create("payment", paymentElementOptions);
    paymentElement.mount("#payment-element");

    const paymentWrapper = document.querySelector(".payment_wrapper");
    paymentWrapper.classList.remove("hidden");
}

async function submitStripePayment(e) {

    const { paymentIntent } = await stripe.retrievePaymentIntent(payment_intend);

    if(paymentIntent.amount && paymentIntent.amount != parseInt(jQuery("div.price_inner div.total_price span.price").attr("totalPrice") * 100))
    {
        jQuery("div.checkout_buttons_wrapper button#calculate").trigger("click")
        jQuery('div.checkout_processing').hide();
        jQuery('div.checkout_buttons').show();
        return ;
    }

    setLoading(true);
    try {
    const { error,paymentIntent } = await stripe.confirmPayment({

        elements,
        confirmParams: {
            // Make sure to change this to your payment completion page

            payment_method_data: {
                billing_details: {
                    address: {
                        country: 'AU',
                        postal_code: '00000',
                    }

                }
            }
        },
        redirect: "if_required",
    });
        if (error) {
            showMessage(error.message);
        } else if (paymentIntent && paymentIntent.status === "succeeded") {
            submitorder(e,paymentIntent.id,'Payment executed by Stripe Successfully')
        } else {
            showMessage("Payment failed");
        }
    } catch (error) {
        showMessage("An unexpected error occurred.");
    }
    // This point will only be reached if there is an immediate error when
    // confirming the payment. Otherwise, your customer will be redirected to
    // your `return_url`. For some payment methods like iDEAL, your customer will
    // be redirected to an intermediate site first to authorize the payment, then
    // redirected to the `return_url`.
    // if (error.type === "card_error" || error.type === "validation_error") {
    //     showMessage(error.message);
    // } else {
    //     showMessage("An unexpected error occurred.");
    // }

    setLoading(false);
}

// ------- UI helpers -------

function showMessage(messageText) {

    stopLoader();
    jQuery('div.checkout_processing').hide();
    jQuery('div.checkout_buttons').show();

    const messageContainer = document.querySelector("#payment-message");

    messageContainer.classList.remove("hidden");
    messageContainer.textContent = messageText;

    setTimeout(function () {
        messageContainer.classList.add("hidden");
        messageContainer.textContent = "";
    }, 4000);
}

// Show a spinner on payment submission
function setLoading(isLoading) {
    if (isLoading) {
        // Disable the button and show a spinner
        document.querySelector("#confirm").disabled = true;
        document.querySelector("#spinner").classList.remove("hidden");
        document.querySelector("#button-text").classList.add("hidden");
    } else {
        document.querySelector("#confirm").disabled = false;
        document.querySelector("#spinner").classList.add("hidden");
        document.querySelector("#button-text").classList.remove("hidden");
    }
}
