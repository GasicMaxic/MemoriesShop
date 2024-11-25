document.addEventListener("DOMContentLoaded", (event) => {
  var num = document.getElementById("kolicina");

  num.addEventListener("change", () => {
    broj = num.value;
    const lampaItem = document.querySelector(".summary-item p");
    const totalPrice = document.querySelector(".summary-total span");
    const summaryItems = document.querySelectorAll(".summary-item");
    const lampaPrice = summaryItems[0].querySelector("span"); // Price of "Lampa"
    const dostavaPrice = summaryItems[1].querySelector("span"); // Price of "Dostava"
    lampaItem.textContent = broj + "x Lampa";

    lampaInt = broj * 2999;
    dostavaInt = Math.round(parseFloat(lampaInt) / 10);
    lampaPrice.textContent = lampaInt + "rsd";
    //dostavaPrice.textContent = 'Besplatna';
    totalPrice.textContent = lampaInt + "rsd";
    //totalPrice.textContent = lampaInt + dostavaInt + "rsd";
  });
});

document
  .getElementById("order-form")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission

    const formData = new FormData(this); // Collect form data
    const submitButton = document.querySelector("button[type='submit']");
    const spinner = document.getElementById("loading-spinner");

    // Disable the button and show the spinner
    submitButton.disabled = true;
    spinner.classList.remove("hidden");

    fetch("process.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        const popup = document.getElementById("popup");
        const popupMessage = document.getElementById("popup-message");

        if (data.status === "success") {
          popupMessage.textContent = data.message; // Set success message
        } else {
          popupMessage.textContent = data.message; // Set error message
        }

        popup.classList.remove("hidden"); // Show the popup
      })
      .catch((error) => {
        console.error("Error:", error);
        const popup = document.getElementById("popup");
        const popupMessage = document.getElementById("popup-message");

        popupMessage.textContent =
          "An unexpected error occurred. Please try again.";
        popup.classList.remove("hidden"); // Show the popup
      })
      .finally(() => {
        // Re-enable the button and hide the spinner
        submitButton.disabled = false;
        spinner.classList.add("hidden");
      });
  });

// Close the popup when the button is clicked
document.getElementById("close-popup").addEventListener("click", function () {
  const popup = document.getElementById("popup");
  popup.classList.add("hidden"); // Hide the popup
});
