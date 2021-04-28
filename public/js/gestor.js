new Vue({
        el: '#app',
        vuetify: new Vuetify({
            theme: {
                dark: true,
            },
        }),
        data: {
            folder: "",
            files: [],
            carpetas: [
                { title: '...', subtitle: '', hidden: true, },
            ],
            archivos: [

            ],
            dialog: false,
            dialog_delete: false,
            dialog_folder: false,
            nombre_archivo: "",
            nombre_carpeta: "",
            nombre: "",
            icon: 'mdi-folder',
            overlay: false,
        },
    created() {
        $("body").on("contextmenu",function(e){
            return false;
        });
            axios.get('/getarchivos').then(response=>{
                let data = response.data;
                let archivos = data.archivos;
                let carpetas = data.carpetas;
                for(let i = 0; i<archivos.length; i++){
                    archivo = archivos[i][0];
                    arr = archivo.split('.');
                    ext = arr[arr.length-1];
                    icono = '';
                    switch(ext){
                        case 'docx':
                        case 'docm':
                        case 'dotx':
                        case 'dotm': icono = 'mdi-file-word';
                            break;
                        case 'xlsx':
                        case 'xlsm':
                        case 'xltx':
                        case 'xltm':
                        case 'xlsb':
                        case 'xlam': icono = 'mdi-file-excel';
                            break
                        case 'pptx':
                        case 'pptm':
                        case 'ppsx':
                        case 'sldx': icono = 'mdi-file-powerpoint';
                            break;
                         case 'pdf': icono = 'mdi-file-pdf';
                            break;
                        case 'png':
                        case 'jpg':
                        case 'jpeg':
                        case 'svg':
                        case 'eps':
                        case 'bmp':
                        case 'gif':
                        case 'raw': icono = 'mdi-image';
                            break;
                        case 'mkv':
                        case 'webm':
                        case 'avi':
                        case 'wmv':
                        case 'amv':
                        case 'mp4':
                        case '3gp': icono = 'mdi-video';
                            break;
                        case 'mp3':
                        case 'm4a': icono = 'mdi-music';
                            break;
                        case 'zip':
                        case 'rar': icono = 'mdi-zip-box';
                            break;
                        case 'txt':
                        default: icono = 'mdi-file-document';
                            break;
                    }
                    this.archivos.push({title: archivos[i][0]+" - "+archivos[i][2], subtitle: archivos[i][1],
                        icon: icono, nombre:archivos[i][0]});
                }

                for(let i = 2; i<carpetas.length; i++){
                    this.carpetas.push({title: carpetas[i][0], subtitle: carpetas[i][1]});
                }
            });
    },
    methods:{
            openDialog: function () {
                this.dialog = true;
            },
            subir:function (){
                this.overlay = true;
                var formdata = new FormData();
                for(let i=0; i < this.files.length; i++){
                    formdata.append("file"+i,this.files[i]);
                }
                formdata.append('numarch',this.files.length);
                formdata.append('carpeta',this.folder);
                axios.post('/upload',formdata,{headers: {
                        'Content-Type': 'multipart/form-data'
                    }}).then(response => {
                    this.reload();
                });
            },
            nuevaCarpeta:function (){
                axios.post('/crearcarpeta',{
                    carpeta:this.folder,
                    nombre: this.nombre
                }).then(resp => {
                    this.carpetas.push({
                        title: this.nombre,
                        subtitle: Date(),
                        hidden: false
                    });
                    this.dialog = false;
                    this.nombre = "";
                });
            },
            openFolder: function (carpeta){
                if(carpeta == "..."){
                    let arr = this.folder.split('/');
                    arr.pop();
                    this.folder = arr.toString();

                    this.folder = this.folder.replace(",",'/');

                }else{
                    this.folder = this.folder+"/"+carpeta;
                }
                axios.post('/openfolder',{
                    carpeta:this.folder
                }).then(response => {
                    this.archivos = [];
                    this.carpetas = [];
                    this.carpetas.push({ title: '...', subtitle: '', hidden: false, },)
                    let data = response.data;
                    let archivos = data.archivos;
                    let carpetas = data.carpetas;
                    for(let i = 0; i<archivos.length; i++){
                        archivo = archivos[i][0];
                        arr = archivo.split('.');
                        ext = arr[arr.length-1];
                        icono = '';
                        switch(ext){
                            case 'docx':
                            case 'docm':
                            case 'dotx':
                            case 'dotm': icono = 'mdi-file-word';
                                break;
                            case 'xlsx':
                            case 'xlsm':
                            case 'xltx':
                            case 'xltm':
                            case 'xlsb':
                            case 'xlam': icono = 'mdi-file-excel';
                                break
                            case 'pptx':
                            case 'pptm':
                            case 'ppsx':
                            case 'sldx': icono = 'mdi-file-powerpoint';
                                break;
                            case 'pdf': icono = 'mdi-file-pdf';
                                break;
                            case 'png':
                            case 'jpg':
                            case 'jpeg':
                            case 'svg':
                            case 'eps':
                            case 'bmp':
                            case 'gif':
                            case 'raw': icono = 'mdi-image';
                                break;
                            case 'mkv':
                            case 'webm':
                            case 'avi':
                            case 'wmv':
                            case 'amv':
                            case 'mp4':
                            case '3gp': icono = 'mdi-video';
                                break;
                            case 'mp3':
                            case 'm4a': icono = 'mdi-music';
                                break;
                            case 'zip':
                            case 'rar': icono = 'mdi-zip-box';
                                break;
                            case 'txt':
                            default: icono = 'mdi-file-document';
                                break;
                        }
                        this.archivos.push({title: archivos[i][0]+" - "+archivos[i][2], subtitle: archivos[i][1],
                            icon: icono, nombre:archivos[i][0]});
                    }

                    for(let i = 2; i<carpetas.length; i++){
                        this.carpetas.push({title: carpetas[i][0], subtitle: carpetas[i][1]});
                    }
                });
            },
            handler:function (e,nombre_carpeta){
                if(nombre_carpeta == "..."){
                    return;
                }
                this.nombre_carpeta = nombre_carpeta;
                this.dialog_folder = true;
            },
            reload:function (){
                axios.post('/openfolder',{
                    carpeta:this.folder
                }).then(response => {
                    this.archivos = [];
                    this.carpetas = [];
                    this.carpetas.push({ title: '...', subtitle: '', hidden: false, },)
                    let data = response.data;
                    let archivos = data.archivos;
                    let carpetas = data.carpetas;
                    for(let i = 0; i<archivos.length; i++){
                        archivo = archivos[i][0];
                        arr = archivo.split('.');
                        ext = arr[arr.length-1];
                        icono = '';
                        switch(ext){
                            case 'docx':
                            case 'docm':
                            case 'dotx':
                            case 'dotm': icono = 'mdi-file-word';
                                break;
                            case 'xlsx':
                            case 'xlsm':
                            case 'xltx':
                            case 'xltm':
                            case 'xlsb':
                            case 'xlam': icono = 'mdi-file-excel';
                                break
                            case 'pptx':
                            case 'pptm':
                            case 'ppsx':
                            case 'sldx': icono = 'mdi-file-powerpoint';
                                break;
                            case 'pdf': icono = 'mdi-file-pdf';
                                break;
                            case 'png':
                            case 'jpg':
                            case 'jpeg':
                            case 'svg':
                            case 'eps':
                            case 'bmp':
                            case 'gif':
                            case 'raw': icono = 'mdi-image';
                                break;
                            case 'mkv':
                            case 'webm':
                            case 'avi':
                            case 'wmv':
                            case 'amv':
                            case 'mp4':
                            case '3gp': icono = 'mdi-video';
                                break;
                            case 'mp3':
                            case 'm4a': icono = 'mdi-music';
                                break;
                            case 'zip':
                            case 'rar': icono = 'mdi-zip-box';
                                break;
                            case 'txt':
                            default: icono = 'mdi-file-document';
                                break;
                        }
                        this.archivos.push({title: archivos[i][0]+" - "+archivos[i][2], subtitle: archivos[i][1],
                            icon: icono, nombre:archivos[i][0]});
                    }

                    for(let i = 2; i<carpetas.length; i++){
                        this.carpetas.push({title: carpetas[i][0], subtitle: carpetas[i][1]});
                    }
                    this.overlay = false;
                    this.files = [];
                });
            },
            download:function (nombre){
                console.log(nombre)
                nombre = nombre.replaceAll(" ", "+")
                let folder = this.folder.replaceAll("/", ".")
                window.location.href = "/download?carpeta="+folder+"&archivo="+nombre;
            },
            dialogEliminar: function (nombre) {
                this.dialog_delete = true;
                this.nombre_archivo = nombre;
            },
            eliminar:function (){
                axios.post('/eliminar',{
                    carpeta:this.folder,
                    nombre: this.nombre_archivo
                }).then(resp => {
                    if(resp.data.code == 200){
                        let index =  -1;
                        for(let i = 0; i<this.archivos.length; i++){
                            if(this.archivos[i].nombre == this.nombre_archivo){
                                index = i;
                                console.log(index);
                            }
                        }

                        if(index > -1){
                            this.archivos.splice(index,1);
                        }
                    }
                    this.dialog_delete = false;
                });
            },
            eliminarFolder:function (){
               axios.post('/eliminarcarpeta',{
                    carpeta:this.folder,
                    nombre: this.nombre_carpeta
                }).then(resp => {
                    if(resp.data.code == 200){
                        let index =  -1;
                        for(let i = 0; i<this.carpetas.length; i++){
                            if(this.carpetas[i].title == this.nombre_carpeta){
                                index = i;
                            }
                        }

                        if(index > -1){
                            this.carpetas.splice(index,1);
                        }
                    }
                    this.dialog_folder = false;
                });
            },
        }
    });
