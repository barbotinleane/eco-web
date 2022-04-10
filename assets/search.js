const $field = $("#search_search");

$field.keyup(function (event) {
    if(this.value !== null && this.value !== "") {
        href = "/formations/"+this.value;
    } else {
        href = "/formations/all";
    }

    $.getJSON(href, null, function(result) {
        formations = JSON.stringify(result);

        formationsDisplayed = $("#formations");
        formationsDisplayed.empty();

        $.each(result['formations'], function(key, value) {
            const formation = document.createElement("div");
            formation.classList.add("col-12", "col-sm-6", "col-md-4");

            const box = document.createElement("div");
            box.classList.add("bg-dark", "text-white", "rounded-corners", "p-4", "m-2");

            const img = document.createElement("img");
            img.classList.add("w-100");
            img.src = value['image'];

            const title = document.createElement("h2");
            title.classList.add("text-space-mono", "text-light", "mt-4");
            title.textContent = value['title'];

            const description = document.createElement("p");
            description.innerText = value['description'];

            const button = document.createElement("button");
            button.classList.add("button-primary");
            button.innerText = "Accéder à la formation";
            box.append(img, title, description, button);

            formation.append(box);
            formationsDisplayed.append(formation);
        })
    })

})