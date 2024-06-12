<div class="container-fluid">
    <form id="uploadForm" class="needs-validation" enctype="multipart/form-data">
        <div>
            <label for="image">Image:</label>
            <input class="form-control pb-3" type="file" id="image" name="image" accept="image/*" required>
        </div>
        <div>
            <label for="title">Title:</label>
            <input class="form-control pb-3" type="text" id="title" name="title" required>
        </div>
        <div>
            <label for="article-text">Content:</label>
            <textarea class="form-control pb-3" id="article-text" name="article-text" required></textarea>
        </div>
        <div>
            <label for="country">Country:</label>
            <input class="form-control pb-3" type="text" id="country" name="country" required>
        </div>
        <button onclick="createArticle()" class="btn btn-primary mb-3 mt-3">Create new article</button>
    </form>
    <hr>
    <div id="articleListAdmin" class="d-flex flex-wrap p-2"></div>
</div>
<!--Ovdje je modal koji se prikaze na editu-->
<!-- Modal -->
<div id="editModal" class="modal" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Article</h5>
            </div>
            <div class="modal-body">
                <form id="editArticleForm">
                    <input type="hidden" id="editArticleId">
                    <div class="form-group">
                        <label for="editArticleTitle">Title</label>
                        <input type="text" class="form-control" id="editArticleTitle">
                    </div>
                    <div class="form-group">
                        <label for="editArticleContent">Content</label>
                        <textarea class="form-control" id="editArticleContent"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editArticleCountry">Country</label>
                        <input type="text" class="form-control" id="editArticleCountry">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="updateArticle()">Save changes</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>
</div>
