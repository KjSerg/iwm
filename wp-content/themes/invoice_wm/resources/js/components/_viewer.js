document.addEventListener("DOMContentLoaded", function () {
    let postId = parseInt(document.body.getAttribute("data-post-id"));
    let userSessionId = Math.random().toString(36).substr(2, 9);

    function updateLiveUsers() {
        fetch(adminAjax, {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: `action=track_live_users&post_id=${postId}&session_id=${userSessionId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Кількість активних користувачів:", data.data.users);
                    document.querySelectorAll('.live-users-count').forEach(function (item) {
                        item.innerText = data.data.users;
                    })
                }
            });
    }

    function removeUser() {
        navigator.sendBeacon(adminAjax, `action=remove_live_user&post_id=${postId}&session_id=${userSessionId}`);
    }

    updateLiveUsers();
    setInterval(updateLiveUsers, 15000);
    window.addEventListener("beforeunload", removeUser);
});
