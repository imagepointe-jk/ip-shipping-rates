(function run() {
  document.addEventListener("DOMContentLoaded", () => {
    const insuranceCheckbox = document.getElementById("ip_shipping_insurance");
    if (!insuranceCheckbox) return;

    insuranceCheckbox.addEventListener("change", () => {
      //update_checkout is an event WC listens for; it does an AJAX update in response
      document.body.dispatchEvent(new Event("update_checkout"));
    });
  });
})();
