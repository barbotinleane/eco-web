const addTagFormDeleteLink = (item) => {
    const boxCenter = document.createElement('div');
    boxCenter.classList.add("text-center", "p-2");

    const removeFormButton = document.createElement('button');
    removeFormButton.classList.add("button-secondary");
    removeFormButton.innerText = 'Supprimer la réponse';

    boxCenter.append(removeFormButton);
    item.append(boxCenter);

    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        // remove the li for the tag form
        item.remove();
    });
}

const addAnswerToCollection = (e) => {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

    const item = document.createElement('li');

    item.innerHTML = collectionHolder
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionHolder.dataset.index
        );

    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;

    addTagFormDeleteLink(item);
};

document
    .querySelectorAll('.add_answer_link')
    .forEach(btn => {
        btn.addEventListener("click", addAnswerToCollection)
    });

document
    .querySelectorAll('ul.tags li')
    .forEach((tag) => {
        addTagFormDeleteLink(tag)
    })

