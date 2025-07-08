// Menambahkan item manual
document.getElementById('addItemForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const newItemInput = document.getElementById('newItem');
    const itemName = newItemInput.value.trim();

    if (itemName !== "") {
        fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `action=add&item=${encodeURIComponent(itemName)}`
        })
        .then(response => response.json())
        .then(data => {
            const ul = document.getElementById('shoppingList');
            ul.innerHTML = ""; // Kosongkan dan muat ulang
            data.forEach((item, index) => {
                const li = document.createElement('li');
                li.dataset.index = index;
                li.innerHTML = `${item} <button class="delete">Hapus</button>`;
                ul.appendChild(li);
            });
            newItemInput.value = "";
        });
    }
});

// Menambahkan item dari daftar tersedia
document.querySelectorAll('.addToCart').forEach(button => {
    button.addEventListener('click', function() {
        const item = this.dataset.item;

        fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `action=add&item=${encodeURIComponent(item)}`
        })
        .then(response => response.json())
        .then(data => {
            const ul = document.getElementById('shoppingList');
            ul.innerHTML = ""; // Kosongkan dan muat ulang
            data.forEach((item, index) => {
                const li = document.createElement('li');
                li.dataset.index = index;
                li.innerHTML = `${item} <button class="delete">Hapus</button>`;
                ul.appendChild(li);
            });
        });
    });
});

// Menghapus item
document.getElementById('shoppingList').addEventListener('click', function(e) {
    if (e.target.classList.contains('delete')) {
        const li = e.target.parentElement;
        const index = li.dataset.index;

        fetch(`index.php?remove=${index}`, { method: 'GET' })
            .then(() => {
                li.remove();
            });
    }
});