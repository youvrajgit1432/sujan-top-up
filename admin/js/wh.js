$(document).ready(function () {
    // Handle delete action
    $(".delete-button").click(function (e) {
        e.preventDefault();
        if (confirm("Are you sure you want to delete this order?")) {
            const id = $(this).data("id");
            const gameType = $(this).data("game-type");
            $.ajax({
                url: "wdelete_order.php",
                method: "POST",
                data: { action: "delete", id: id, game_type: gameType },
                dataType: "json",
                success: function (response) {
                    alert(response.message);
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        }
    });

    // Handle update status action
    $(".update-status").click(function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        const gameType = $(this).data("game-type");
        const status = $(this).data("status");
        $.ajax({
            url: "wupdate_status.php",
            method: "POST",
            data: { action: "update", id: id, game_type: gameType, status: status },
            dataType: "json",
            success: function (response) {
                alert(response.message);
                if (response.success) {
                    location.reload();
                }
            },
            error: function () {
                alert("An error occurred. Please try again.");
            }
        });
    });
});