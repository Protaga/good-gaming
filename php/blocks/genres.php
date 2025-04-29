<aside class="col-md-3">
    <div class="p-3 mb-3 bg-primary text-white rounded" style="max-width: 250px;">
        <h4 class="text-center"><b>Search & Filters</b></h4>

        <form id="filter-form" class="d-flex flex-column">
            <div class="mb-3">
                <input type="text" name="search" class="form-control" placeholder="Search games...">
            </div>

            <div class="mb-3">
                <select name="developer" class="form-select" id="developer-select">
                    <option value="">All Developers</option>
                </select>
            </div>

            <div class="mb-3">
                <select name="genre" class="form-select" id="genre-select">
                    <option value="">All Genres</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="price_min" class="form-label">Price Range</label>
                <div class="d-flex">
                    <input type="number" name="price_min" class="form-control me-2" placeholder="Min" min="0">
                    <input type="number" name="price_max" class="form-control" placeholder="Max" min="0">
                </div>
            </div>

            <div class="mb-3">
                <label for="created_after" class="form-label">Created after</label>
                <input type="date" name="created_after" class="form-control">
            </div>

            <button type="submit" class="btn btn-light text-primary mt-3">Apply Filters</button>
        </form>
    </div>
</aside>

<!-- Контейнер для результатів -->
<div id="games-list" class="mt-4"></div>

<script>
// Завантаження варіантів для селектів (жанрів та розробників)
function loadSelectOptions() {
    fetch('../load_options.php')
        .then(response => response.json())
        .then(data => {
            // console.log(data);

            const developerSelect = document.getElementById('developer-select');
            const genreSelect = document.getElementById('genre-select');

            data.developers.forEach(dev => {
                const option = document.createElement('option');
                option.value = dev.id;
                option.textContent = dev.name;
                developerSelect.appendChild(option);
            });

            data.genres.forEach(genre => {
                const option = document.createElement('option');
                option.value = genre;
                option.textContent = genre;
                genreSelect.appendChild(option);
            });
        });
}

// Пошук без перезавантаження
document.getElementById('filter-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    const queryString = new URLSearchParams(formData).toString();

    fetch('../search.php?' + queryString)
        .then(response => response.json())
        .then(gameNames => {
            const gameItems = document.querySelectorAll('.game-item');

            gameItems.forEach(item => {
                const gameTitle = item.querySelector('h5').innerText.trim();
                if (gameNames.includes(gameTitle)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
});

// Початкове завантаження варіантів селектів
loadSelectOptions();
</script>

