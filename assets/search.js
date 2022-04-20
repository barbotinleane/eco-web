const $field = $("#search_search");

$field.keyup(function (event) {
    if(this.value !== null && this.value !== "") {
        href = "/json/formations/"+this.value;
    } else {
        href = "/json/formations/all";
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
            img.src = 'build/photos/formation/'+value['image'];

            const title = document.createElement("h2");
            title.classList.add("text-space-mono", "text-light", "mt-4");
            title.textContent = value['title'];

            const description = document.createElement("p");
            description.innerText = value['description'];

            const button = document.createElement("button");
            button.classList.add("button-primary");
            button.innerText = "Accéder à la formation";

            if(value['progress'] !== undefined) {
                const progress = document.createElement("div");

                const label = document.createElement("p");
                label.classList.add("text-secondary");
                label.innerText = "Votre progression : ";

                const barContainer = document.createElement("div");
                barContainer.classList.add("progress");

                const bar = document.querySelector('#Hidden-Elements div.progress-bar').cloneNode(true)
                bar.setAttribute("style", "width: "+value['progress']+"%;");
                bar.setAttribute("aria-valuenow", value['progress']);
                bar.innerText = value['progress']+"%";

                barContainer.append(bar);
                progress.append(label, barContainer);
                box.append(img, title, description, progress, button);
            } else {
                box.append(img, title, description, button);
            }

            formation.append(box);
            formationsDisplayed.append(formation);
        })
    })
})

const $progress = $('input[name="progress-filter"]');

$progress.change(function (event) {
    if($(this).val() !== null && $(this).val() !== "") {
        href = "/json/formation/"+this.value;
    } else {
        href = "/json/formations/all";
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
            img.src = 'build/photos/formation/'+value['image'];

            const title = document.createElement("h2");
            title.classList.add("text-space-mono", "text-light", "mt-4");
            title.textContent = value['title'];

            const description = document.createElement("p");
            description.innerText = value['description'];

            const button = document.createElement("a");
            button.classList.add("button-primary");
            button.href = "formations/"+value['id'];
            button.innerText = "Accéder à la formation";

            if(value['progress'] !== undefined) {
                const progress = document.createElement("div");

                const label = document.createElement("p");
                label.classList.add("text-secondary");
                label.innerText = "Votre progression : ";

                const barContainer = document.createElement("div");
                barContainer.classList.add("progress");

                const bar = document.querySelector('#Hidden-Elements div.progress-bar').cloneNode(true)
                bar.setAttribute("style", "width: "+value['progress']+"%;");
                bar.setAttribute("aria-valuenow", value['progress']);
                bar.innerText = value['progress']+"%";

                barContainer.append(bar);
                progress.append(label, barContainer);
                box.append(img, title, description, progress, button);
            } else {
                box.append(img, title, description, button);
            }

            formation.append(box);
            formationsDisplayed.append(formation);
        })
    })
});