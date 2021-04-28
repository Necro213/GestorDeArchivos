<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    </head>
    <body style="background: #0a0a0a">
        <v-app id="app" dark>
            <div class="row">
                <div class="col-12">
                    <h2 style="color: snow; text-align: center">Gestor de archivos</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 col-sm-1"></div>
                <div class="col-md-8 col-sm-10">
                    <template>
                        <v-row>
                            <v-col cols="10">
                                <v-file-input
                                    small-chips
                                    multiple
                                    dark
                                    label="Agregar Archivos"
                                    v-model="files"
                                ></v-file-input>
                            </v-col>
                            <v-col cols="2">
                                <v-btn block v-on:click="subir">Subir</v-btn>
                            </v-col>
                        </v-row>
                    </template>
                </div>
                <div class="col-md-2 col-sm-1"></div>
            </div>
            <div class="row">
                <div class="col-md-2 col-sm-1"></div>
                <div class="col-md-8 col-sm-10">
                    <template>
                        <div>
                            <v-btn block small dark v-on:click="openDialog">Crear Carpeta</v-btn>
                        </div>
                    </template>
                </div>
                <div class="col-md-2 col-sm-1"></div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h2 style="color: snow; text-align: center">Archivos@{{ folder }}</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 col-sm-1"></div>
                <div class="col-md-8 col-sm-10">
                    <template>
                      <v-list two-line subheader dark>
                                <v-subheader inset>Carpetas</v-subheader>

                                <v-list-item
                                    v-for="item in carpetas"
                                    :key="item.title"
                                    @click="openFolder(item.title)"
                                    @contextmenu="handler($event,item.title)"
                                    :disabled="item.hidden"
                                >
                                    <v-list-item-avatar>
                                        <v-icon>mdi-folder</v-icon>
                                    </v-list-item-avatar>

                                    <v-list-item-content>
                                        <v-list-item-title v-text="item.title"></v-list-item-title>
                                        <v-list-item-subtitle v-text="item.subtitle"></v-list-item-subtitle>
                                    </v-list-item-content>

                                    <v-list-item-action>
                                        <v-btn icon >
                                            <v-icon color="grey lighten-1">mdi-chevron-right</v-icon>
                                        </v-btn>
                                    </v-list-item-action>
                                </v-list-item>

                                <v-divider inset></v-divider>

                                <v-subheader inset>Archivos</v-subheader>

                                <v-list-item
                                    v-for="item in archivos"
                                    :key="item.title"
                                >
                                    <v-list-item-avatar>
                                        <v-icon>@{{ item.icon }}</v-icon>
                                    </v-list-item-avatar>

                                    <v-list-item-content>
                                        <v-list-item-title v-text="item.title"></v-list-item-title>
                                        <v-list-item-subtitle v-text="item.subtitle"></v-list-item-subtitle>
                                    </v-list-item-content>

                                    <v-list-item-action>
                                        <v-row>
                                            <v-col cols="3" md="3" sm="3"></v-col>
                                            <v-col cols="3" md="3" sm="3">
                                                <v-btn icon>
                                                    <v-icon color="grey lighten-1" v-on:click="download(item.nombre)">mdi-download</v-icon>
                                                </v-btn>
                                            </v-col>
                                            <v-col cols="3" md="3" sm="3">
                                                <v-btn icon>
                                                    <v-icon color="grey lighten-1" v-on:click="dialogEliminar(item.nombre)">mdi-delete</v-icon>
                                                </v-btn>
                                            </v-col>
                                        </v-row>
                                    </v-list-item-action>
                                </v-list-item>
                            </v-list>
                    </template>
                </div>
                <div class="col-md-2 col-sm-1"></div>
            </div>
            <template>
                <v-row justify="center">
                    <v-dialog v-model="dialog" persistent max-width="300px">
                        <v-card>
                            <v-card-title>
                                <span class="headline">Nueva Carpeta</span>
                            </v-card-title>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field label="Nombre" required v-model="nombre"></v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click="dialog = false">Cancelar</v-btn>
                                <v-btn color="blue darken-1" text @click="nuevaCarpeta">Crear</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-row>
            </template>
            <template>
                <v-row justify="center">
                    <v-dialog v-model="dialog_delete" persistent max-width="300px">
                        <v-card>
                            <v-card-title>
                                <span class="headline">Eliminar</span>
                            </v-card-title>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12">
                                            <p>Esta seguro que desea eliminar el archivo @{{nombre_archivo}}</p>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click="dialog_delete = false">Cancelar</v-btn>
                                <v-btn color="blue darken-1" text @click="eliminar">Eliminar</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-row>
            </template>
            <template>
                <v-row justify="center">
                    <v-dialog v-model="dialog_folder" persistent max-width="300px">
                        <v-card>
                            <v-card-title>
                                <span class="headline">Eliminar</span>
                            </v-card-title>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12">
                                            <p>Esta seguro que desea eliminar la carpeta @{{nombre_carpeta}} y todo su contenido</p>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click="dialog_folder = false">Cancelar</v-btn>
                                <v-btn color="blue darken-1" text @click="eliminarFolder">Eliminar</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-row>
            </template>
            <v-overlay :value="overlay">
                <v-progress-circular indeterminate size="64"></v-progress-circular>
            </v-overlay>
        </v-app>
        <script
            src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.20.0/axios.js" integrity="sha512-nqIFZC8560+CqHgXKez61MI0f9XSTKLkm0zFVm/99Wt0jSTZ7yeeYwbzyl0SGn/s8Mulbdw+ScCG41hmO2+FKw==" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/downloadjs/1.4.8/download.min.js"></script>
        <script src="{{asset('/js/gestor.js')}}"></script>
    </body>
</html>
