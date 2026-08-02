"use strict";

document.addEventListener('click', function (e) {
    const dropdownButton = e.target.closest('.dropdown-btn');

    if (dropdownButton) {
        e.stopPropagation();

        const dropdownGroup = dropdownButton.parentElement;
        if (!dropdownGroup) return;
        const dropdownList = dropdownGroup.querySelector('.dropdown-list');

        if (dropdownList) {
            if (!dropdownList.classList.contains('active')) {
                const allDropdownLists = document.querySelectorAll('.dropdown-list');
                allDropdownLists.forEach(list => list.classList.remove('active'));
            }

            dropdownList.classList.toggle('active');
        }
    } else {
        document.querySelectorAll('.dropdown-list').forEach(function (list) {
            list.classList.remove('active');
        });
    }
});
