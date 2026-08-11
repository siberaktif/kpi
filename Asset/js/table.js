document.addEventListener('click', function (event) {
    const element = event.target.closest('.click-truncate');

    // Close when clicking outside
    if (!element) {
        document
            .querySelectorAll('.click-truncate.expanded')
            .forEach(function (item) {
                item.classList.remove('expanded');
            });

        return;
    }

    // Don't activate if empty
    if (element.textContent.trim() === '' || element.textContent.trim() === '-') {
        return;
    }

    // Close other expanded elements
    document
        .querySelectorAll('.click-truncate.expanded')
        .forEach(function (item) {
            if (item !== element) {
                item.classList.remove('expanded');
            }
        });

    element.classList.toggle('expanded');
});