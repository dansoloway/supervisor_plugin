document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".sortable").forEach(function (el) {
        console.log("Sortable initialized on:", el);

        new Sortable(el, {
            animation: 150,
            ghostClass: "sortable-ghost",
            onEnd: function (evt) {
                console.log("Item moved from index", evt.oldIndex, "to", evt.newIndex);

                let itemOrder = [];
                evt.to.querySelectorAll(".sortable-item").forEach(function (item, index) {
                    itemOrder.push({ id: item.dataset.id, order: index + 1 });
                });

                console.log("Sending updated order:", itemOrder);

                fetch(qaBibAjax.ajaxurl, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        action: "save_item_order", // Include the action
                        security: qaBibAjax.nonce, // Include nonce
                        items: itemOrder, // Data to save
                    }),
                })
                    .then((response) => response.json())
                    .then((data) => console.log("AJAX response:", data))
                    .catch((error) => console.error("AJAX error:", error));
            }
        });
    });
});