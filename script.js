// script.js
function showMessage(message, isSuccess) {
    let messageBox = document.getElementById('messageBox');
    messageBox.classList.remove("bg-danger");
    messageBox.classList.remove("bg-success");
    let bgColor = isSuccess ? 'bg-success' : 'bg-danger';
    messageBox.classList.add(bgColor);
    messageBox.innerHTML = message;
    messageBox.style.display = 'block';

    setTimeout(function() {
        messageBox.style.display = 'none';
    }, 2000);
}

function refreshTime() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "./scripts/time.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var currentTime = new Date(xhr.responseText);
            var options = {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            var formattedTime = currentTime.toLocaleString('en-US', options);
            document.getElementById("time").innerHTML = formattedTime;
        }
    }
    xhr.send();
}

setInterval(refreshTime, 1000); // osvjezi da se prikaze vrijeme (1 second)
async function changeContent(path, id) {
    let btnChangeClass = 'bg-dark-subtle'
    try {
        document.getElementsByClassName(btnChangeClass).item(0).classList.remove(btnChangeClass);
    } catch (ex) {
        console.log(ex);
    }
    fetch(path)
        .then(response => response.text())
        .then(text => document.getElementById('content').innerHTML = text);
    currentPage = path;
    if (currentPage === 'pages/home.php') {
        document.getElementById('home').classList.add(btnChangeClass);
        await getArticles();
    } else if (currentPage === 'pages/us.php') {
        document.getElementById('us').classList.add(btnChangeClass);
        await getArticles();
    } else if (currentPage === 'pages/world.php') {
        document.getElementById('world').classList.add(btnChangeClass);
        await getArticles();
    } else if (currentPage === 'pages/administration.php') {
        document.getElementById('admin').classList.add(btnChangeClass);
        await getArticlesAdmin();
        document.getElementById('uploadForm').addEventListener('submit', function (event) {
            event.preventDefault();
        });
    } else if (currentPage === 'pages/article.php') {
        await getArticle(id);
    }
}

window.onload = function () {
    refreshTime(); // Initial call to display the time immediately!
    changeContent('pages/home.php')
}

async function getArticle(id) {
    try {
        await fetch('./scripts/database.php')
            .then(response => response.json())
            .then(data => {
                const articleData = document.getElementById('articleData');
                if (data.error) {
                    articleData.innerHTML = `<p>${data.error}</p>`;
                    showMessage('Error fetching article data!', false);
                } else {
                    data = data.filter(article => String(article["id"]) === String(id));
                    console.log(data[0])
                    if (data.length > 0) {
                        let article = data[0];
                        document.getElementById("articleTitle").innerHTML = `<h2>${article.title}</h2>`;
                        document.getElementById("articleImageContainer").innerHTML = `<img alt="Article Image" class="flex" src='${article["image_path"]}'/>`
                        document.getElementById("articleContent").innerHTML = `<article>${article.content}</article>`;
                    } else {
                        showMessage("No article with this id", false);
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching articles:', error);
                document.getElementById('articleList').innerHTML = `<p>Error fetching countries</p>`;
            });
    } catch (e) {
        console.log(e)
    }
}

async function getArticles() {
    try {
        await fetch('./scripts/database.php')
            .then(response => response.json())
            .then(data => {
                const articleList = document.getElementById('articleList');
                if (data.error) {
                    articleList.innerHTML = `<p>${data.error}</p>`;
                } else {
                    if(articleList.className.includes('world')) {
                        data = data.filter(item => item["country"].includes('world'))
                    } else if (articleList.className.includes('us')) {
                        data = data.filter(item => item["country"].includes('us'))
                    }
                    let countrySet = new Set();
                    data.forEach(article => countrySet.add(article["country"]));
                    data.forEach(article => {
                        const articleItem = document.createElement('div');
                        articleItem.classList.add("col-2");
                        articleItem.onclick = async function (ev) {
                            await changeContent("pages/article.php", article.id);
                        };
                        articleItem.innerHTML = `
                            <img src="${article["image_path"]}" class="img-fluid"/>
                            <p>${article.title}</p>
                        `;
                        articleList.appendChild(articleItem);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching articles:', error);
                document.getElementById('articleList').innerHTML = `<p>Error fetching countries</p>`;
            });
    } catch (e) {
        console.log(e)
    }
}

function openEditModal(id, title, content, country) {
    document.getElementById('editArticleId').value = id;
    document.getElementById('editArticleTitle').value = title;
    document.getElementById('editArticleContent').value = content;
    document.getElementById('editArticleCountry').value = country;
    document.getElementById('editModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

async function getArticlesAdmin() {
    try {
        await fetch('./scripts/database.php')
            .then(response => response.json())
            .then(data => {
                const articleList = document.getElementById('articleListAdmin');
                articleList.innerHTML = '';
                let countrySet = new Set();
                data.forEach(article => countrySet.add(article["country"]));
                if (data.error) {
                    articleList.innerHTML = `<p>${data.error}</p>`;
                    showMessage('Error fetching article data!', false);
                } else {
                    data.forEach(article => {
                        const articleItem = document.createElement('div');
                        articleItem.innerHTML = `
                            <div class="d-flex flex-column align-baseline justify-content-between m-2 border border-secondary rounded p-2">
                                <h3 class="m-2">${article.title}</h3>
                                <p class="m-2">${article.id}</p>
                                <button  class="btn btn-primary edit-button m-2" 
                                data-id="${article.id}" 
                                data-title="${article.title}" 
                                data-content="${article.content}"
                                data-country="${article.country}">EDIT</button>
                                <button onclick="deleteArticle(${article.id})" class="btn btn-warning">DELETE</button>
                            </div>
                        `;
                        articleList.appendChild(articleItem);
                    });

                    document.querySelectorAll('.edit-button').forEach(button => {
                        button.addEventListener('click', (event) => {
                            const articleId = event.target.getAttribute('data-id');
                            const articleTitle = event.target.getAttribute('data-title');
                            const articleContent = event.target.getAttribute('data-content');
                            const articleCountry = event.target.getAttribute('data-country');
                            openEditModal(articleId, articleTitle, articleContent, articleCountry);
                        });
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching articles:', error);
                document.getElementById('articleList').innerHTML = `<p>Error fetching countries</p>`;
            });
    } catch (e) {
        console.log(e)
    }
}

(function () {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
})()


async function createArticle() {
    var formData = new FormData();
    var imageFile = document.getElementById('image').files[0];
    formData.append('image', imageFile);
    formData.append('title', document.getElementById('title').value);
    formData.append('article-text', document.getElementById('article-text').value);
    formData.append('country', document.getElementById('country').value);

    if(
        document.getElementById('title').value === '' ||
        document.getElementById('article-text').value === '' ||
        document.getElementById('country').value === ''
    ) {
        showMessage('Please fill out the fields properly...', false);
    } else {
        await fetch('./scripts/database.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showMessage('Error creating article!', false);
                } else {
                    showMessage('Article successfully created!', true); // Show success message
                }
            })
            .then(getArticlesAdmin)
            .catch(error => {
                showMessage(`Database issue!`, false); // Show error message
            });
    }
}

function updateArticle() {
    const id = document.getElementById('editArticleId').value;
    const title = document.getElementById('editArticleTitle').value;
    const content = document.getElementById('editArticleContent').value;
    const country = document.getElementById('editArticleCountry').value;

    const formData = {
        id: id,
        title: title,
        content: content,
        country: country
    };

    fetch('./scripts/database.php', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)})
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            } else {
                showMessage('Article successfully updated!', true); // Show success message
            }
        })
        .then(getArticlesAdmin)
        .catch(error => {
            showMessage(`Error: ${error.message}`, false); // Show error message
        });
}

function deleteArticle(id) {
    fetch(`./scripts/database.php?id=${id}`, {
        method: 'DELETE'
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            } else {
                showMessage('Article successfully deleted!', true); // Show success message
            }
        })
        .then(getArticlesAdmin)
        .catch(error => {
            showMessage(`Error: ${error.message}`, false); // Show error message
        });
}

