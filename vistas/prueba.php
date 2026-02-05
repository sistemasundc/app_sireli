<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uppy Drag & Drop</title>

    <!-- Uppy CSS -->
    <link href="https://releases.transloadit.com/uppy/v3.9.0/uppy.min.css" rel="stylesheet">

    <style>
        #files-drag-drop {
            margin: 20px;
            padding: 20px;
            border: 2px dashed #007bff;
            min-height: 100px;
        }

        .uploaded-files {
            margin-top: 20px;
        }

        .uploaded-files ul {
            list-style-type: none;
            padding: 0;
        }

        .uploaded-files li {
            margin: 5px 0;
        }
    </style>
</head>
<body>

    <div id="files-drag-drop">
        <div class="for-DragDrop"></div>
        <div class="for-ProgressBar mt-3"></div>
        <div class="uploaded-files mt-3">
            <h5>Archivos seleccionados:</h5>
            <ul id="lista-archivos"></ul>
        </div>
    </div>

    <!-- Uppy JS -->
    <script src="https://releases.transloadit.com/uppy/v3.9.0/uppy.min.js"></script>

    <script>
        // Inicializamos Uppy
        const uppy = new Uppy.Core({
            restrictions: {
                maxNumberOfFiles: 5,
                allowedFileTypes: ['.pdf', '.docx', '.jpg', '.png']
            },
            autoProceed: false // No se suben los archivos automáticamente
        });

        // Agregar el plugin DragDrop
        uppy.use(Uppy.DragDrop, {
            target: '.for-DragDrop',
            note: 'Arrastra archivos o haz clic para seleccionar'
        });

        // Agregar el plugin ProgressBar
        uppy.use(Uppy.ProgressBar, {
            target: '.for-ProgressBar',
            showProgressDetails: true
        });

        // Agregar el plugin XHRUpload para subir los archivos
        uppy.use(Uppy.XHRUpload, {
            endpoint: '../archivos',  // Reemplaza esto con tu endpoint real
            fieldName: 'file',
            formData: true,
        });

        // Mostrar archivos seleccionados en la lista
        uppy.on('file-added', (file) => {
            const li = document.createElement('li');
            li.id = file.id;
            li.innerHTML = `
                ${file.name}
                <button onclick="eliminarArchivo('${file.id}')">Eliminar</button>
            `;
            document.getElementById('lista-archivos').appendChild(li);
        });

        // Eliminar archivo
        function eliminarArchivo(id) {
            uppy.removeFile(id);
            const li = document.getElementById(id);
            if (li) li.remove();
        }

        // Mostrar progreso de carga
        uppy.on('upload-progress', (file, progress) => {
            console.log(`Subiendo ${file.name}: ${progress.percentage}%`);
        });
    </script>
</body>
</html>
