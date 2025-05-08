<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container my-5" style="max-width: 720px;">
    <h1 class="mb-4">Nueva noticia</h1>

    <form method="post" action="noticia_guardar.php" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Extracto (máx. 160 car.)</label>
            <textarea name="excerpt" class="form-control" maxlength="160" rows="2" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Contenido</label>
            <!-- Con TinyMCE o CKEditor obtienes un WYSIWYG -->
            <textarea name="contenido" id="editor" rows="10" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Imagen destacada (JPEG/PNG)</label>
            <input type="file" name="imagen" accept=\"image/*\" class=\"form-control\" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href=\"/admin/noticias_lista.php\" class=\"btn btn-secondary\">Cancelar</a>
    </form>
</div>

<script src=\"https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js\" referrerpolicy=\"origin\"></script>
<script>
    tinymce.init({
        selector: '#editor',
        height: 400,
        menubar: false
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>