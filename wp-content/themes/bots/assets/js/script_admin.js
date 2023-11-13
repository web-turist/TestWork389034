deletButton = document.querySelector('.delete_img_button'),
addingButton = document.querySelector('.add_img_button'),
imageForDelete = document.querySelector('.image_for_delete');


console.log('object');
imageForDelete.style.display = 'block'
deletButton.addEventListener('click', () => {
    imageForDelete.style.display = 'none';
});

addingButton.addEventListener('click', () => {
    imageForDelete.style.display = 'block';
});
