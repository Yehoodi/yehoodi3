<fieldset id="preview-images">
    <legend>Images</legend>

    <form method="post"
          action="{geturl controller='Resourcemanager' action='images'}"
          enctype="multipart/form-data">

        <div>
            <input type="hidden" name="id" value="{$fp->resource->getId()}" />
            <input type="file" name="image" />
            <input type="submit" value="Upload Image" name="upload" />
        </div>
    </form>
</fieldset>
