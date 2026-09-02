<?php use App\Core\Lang; ?>

<div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg max-w-3xl mx-auto">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-extrabold text-[#1f3766] dark:text-gray-100 mb-2">
            <?= Lang::get('public_form_title') ?>
        </h1>
        <p class="text-gray-500 dark:text-gray-400">
            <?= Lang::get('public_from_subtitle') ?>
        </p>
    </div>

    <?php if (isset($status)) : ?>
        <div class="mb-6 p-4 rounded-md <?= $status['success'] ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'; ?>">
            <?= htmlspecialchars($status['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form action="/" method="POST" enctype="multipart/form-data" id="pqrsf-form" class="max-w-3xl mx-auto">
        
        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 border-b-2 border-gray-400 dark:border-gray-700 pb-2 mb-8">
            <?= Lang::get('patient_data_title') ?>
        </h2>

        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-6 group">
                <select id="tipo_documento" name="tipo_documento" class="block py-2.5 px-2 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required>
                    <option value="" selected></option>
                    <?php foreach ($tiposId as $tipo) : ?>
                        <option value="<?= htmlspecialchars($tipo->tipo_id_paciente, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($tipo->descripcion, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="tipo_documento" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"><?= Lang::get('patient_id_type') ?> <span class="text-red-500">*</span></label>
            </div>

            <div class="relative z-0 w-full mb-6 group">
                <input type="text" id="documento" name="documento" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                <label for="documento" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"><?= Lang::get('patient_document_number') ?> <span class="text-red-500">*</span></label>
            </div>
        </div>

        <div class="relative z-0 w-full mb-6 group">
            <input type="text" id="nombres" name="nombres" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
            <label for="nombres" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"><?= Lang::get('patient_full_name') ?> <span class="text-red-500">*</span></label>
        </div>

        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-6 group">
                <select id="tipo_solicitante" name="tipo_solicitante" class="block py-2.5 px-2 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required>
                    <option value="" selected></option>
                    <?php foreach ($tiposSolicitante as $tipo) : ?>
                        <option value="<?= htmlspecialchars((string)$tipo->id, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($tipo->descripcion, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="tipo_solicitante" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"><?= Lang::get('applicant_type') ?> <span class="text-red-500">*</span></label>
            </div>

            <div class="relative z-0 w-full mb-6 group">
                <select id="servicio" name="servicio" class="block py-2.5 px-2 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required>
                    <option value="" selected></option>
                    <?php foreach ($serviciosEvento as $servicio) : ?>
                        <option value="<?= htmlspecialchars((string)$servicio->id, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($servicio->descripcion, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="servicio" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"><?= Lang::get('service_event') ?> <span class="text-red-500">*</span></label>
            </div>
        </div>

        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-2 group">
                <input type="email" id="correo_electronico" name="correo_electronico" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                <label for="correo_electronico" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"><?= Lang::get('email') ?> <span class="text-red-500">*</span></label>
                <p class="hidden mt-2 text-sm text-red-600 dark:text-red-500" id="correo_electronico_error"></p>
            </div>

            <div class="relative z-0 w-full mb-2 group">
                <input type="tel" id="telefono_celular" name="telefono_celular" inputmode="tel" autocomplete="tel" maxlength="15" pattern="[0-9\s\(\)\+\-]{7,15}" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                <label for="telefono_celular" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"><?= Lang::get('phone') ?> <span class="text-red-500">*</span></label>
                <p class="hidden mt-2 text-sm text-red-600 dark:text-red-500" id="telefono_celular_error"></p>
            </div>
        </div>

        <div class="mb-8">
            <label for="aseguradora" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= Lang::get('insurer') ?> <span class="text-red-500">*</span></label>
            <select id="aseguradora" name="aseguradora">
                <option value=""><?= Lang::get('insurer_search_placeholder') ?></option>
                <?php foreach ($aseguradoras as $aseguradora) : ?>
                    <option value="<?= htmlspecialchars($aseguradora->tipo_id_tercero . '*_*' . $aseguradora->tercero_id, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($aseguradora->nombre_tercero, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> <?= Lang::get('differential_approach') ?> <span class="text-red-500">*</span> </label>

            <div class="border-0 border-b-2 border-gray-300 dark:border-gray-600 pb-4 max-h-48 overflow-y-auto pr-2">
                <?php foreach (($enfoquesDiferenciales ?? []) as $enfoqueDiferencial) : ?>
                    <?php
                        $valor       = (int) $enfoqueDiferencial->id;
                        $id_checkbox = 'enfoqueDiferencial_' . $valor;
                        $descripcion = $enfoqueDiferencial->descripcion;
                    ?>
                    <div class="flex items-center mb-3 last:mb-0">
                        <input id="<?= htmlspecialchars($id_checkbox, ENT_QUOTES, 'UTF-8') ?>" name="enfoques_diferenciales[]" type="checkbox" value="<?= $valor ?>" class="chk-enfoque w-4 h-4 text-blue-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-500 rounded focus:ring-blue-500 dark:focus:ring-blue-600 cursor-pointer">
                        <label for="<?= htmlspecialchars($id_checkbox, ENT_QUOTES, 'UTF-8') ?>" class="ms-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer"> <?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?></label>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="contenedor_enfoque_diferencial_otro" class="hidden mt-4">
                <div class="relative z-0 w-full mb-4 group">
                    <input type="text" id="enfoque_diferencial_otro" name="enfoque_diferencial_otro" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" ">
                    <label for="enfoque_diferencial_otro" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"> <?= Lang::get('other_please_specify') ?> </label>
                </div>
            </div>
        </div>
        

        <div class="mb-8 my-8">
            <label class="flex items-center group cursor-pointer peer-disabled:cursor-not-allowed">
                <input type="checkbox" id="sameAsPatient" class="peer form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-200 peer-disabled:opacity-50 peer-disabled:cursor-not-allowed"><?= Lang::get('is_patient_same_as_petitioner') ?></span>
            </label>
            <p class="ml-8 text-xs text-gray-500 dark:text-gray-400"><?= Lang::get('is_patient_same_as_petitioner_helper') ?></p>
        </div>
        
        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 border-b-2 border-gray-400 dark:border-gray-700 pb-2 mb-8"><?= Lang::get('petitioner_data_title') ?></h2>
        
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-6 group">
                <select id="tipo_documento_peticionario" name="tipo_documento_peticionario" class="block py-2.5 px-2 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                    <option value="" selected></option>
                    <?php foreach ($tiposId as $tipo) : ?>
                        <option value="<?= htmlspecialchars($tipo->tipo_id_paciente, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($tipo->descripcion, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="tipo_documento_peticionario" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"><?= Lang::get('petitioner_id_type') ?></label>
            </div>

            <div class="relative z-0 w-full mb-6 group">
                <input type="text" id="documento_peticionario" name="documento_peticionario" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                <label for="documento_peticionario" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"><?= Lang::get('petitioner_document_number') ?></label>
            </div>

            <div class="relative z-0 w-full mb-6 group">
                <input type="text" id="nombres_peticionario" name="nombres_peticionario" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                <label for="nombres_peticionario" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"><?= Lang::get('petitioner_names') ?></label>
            </div>

            <div class="relative z-0 w-full mb-6 group">
                <input type="text" id="direccion_peticionario" name="direccion_peticionario" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                <label for="direccion_peticionario" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"><?= Lang::get('petitioner_address') ?></label>
            </div>

            <div class="relative z-0 w-full mb-2 group">
                <input type="email" id="correo_electronico_peticionario" name="correo_electronico_peticionario" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                <label for="correo_electronico_peticionario" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"><?= Lang::get('petitioner_email') ?></label>
                <p class="hidden mt-2 text-sm text-red-600 dark:text-red-500" id="correo_electronico_peticionario_error"></p>
            </div>

            <div class="relative z-0 w-full mb-2 group">
                <input type="tel" id="telefono_celular_peticionario" name="telefono_celular_peticionario" inputmode="tel" autocomplete="tel" maxlength="15" pattern="[0-9\s\(\)\+\-]{7,15}" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                <label for="telefono_celular_peticionario" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"><?= Lang::get('petitioner_phone') ?></label>
                <p class="hidden mt-2 text-sm text-red-600 dark:text-red-500" id="telefono_celular_peticionario_error"></p>
            </div>
        </div>

        <h2 class="my-8 text-xl font-semibold text-gray-700 dark:text-gray-200 border-b-2 border-gray-400 dark:border-gray-700 pb-2 mb-8"><?= Lang::get('facts_description_title') ?></h2>
        
        <div class="relative z-0 w-full mb-6 group">
            <textarea id="observacion" name="observacion" rows="6" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer resize-none" placeholder=" " required></textarea>
            <label for="observacion" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"><?= Lang::get('event_details') ?> <span class="text-red-500">*</span></label>
        </div>

        <div class="mb-6 my-8">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3"><?= Lang::get('attach_files_label') ?></label>
            
            <!-- Área de drag and drop -->
            <div id="drop-area-container" class="w-full">
                <label for="attachments" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-800 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 transition-colors duration-200">
                    <div class="flex flex-col items-center justify-center p-4">
                        <svg class="w-8 h-8 mb-2 text-gray-400 dark:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                        </svg>
                        <p class="mb-1 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold"><?= Lang::get('drag_drop_click') ?></span> <?= Lang::get('drag_drop_or') ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= Lang::get('drag_drop_types') ?></p>
                    </div>
                    <input type="file" name="attachments[]" id="attachments" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif" class="hidden" />
                </label>
            </div>
            
            <!-- Lista de archivos debajo del drag and drop -->
            <div id="file-list" class="hidden mt-4 transition-all duration-300">
                <div class="w-full">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3"><?= Lang::get('file_list_title') ?></p>
                    <!-- Contenedor horizontal que distribuye los archivos uniformemente -->
                    <div class="flex flex-wrap gap-2 justify-start">
                        <div id="file-items" class="flex flex-row gap-2 w-full"></div>
                    </div>
                </div>
            </div>
            
            <div id="file-validation-error" class="text-red-500 text-sm mt-2"></div>
        </div>

        <div class="mb-6 flex items-start justify-between gap-4">
            <div class="flex-1">
                <label class="flex items-start cursor-pointer">
                    <input type="checkbox" name="authorization" value="1" class="form-checkbox h-5 w-5 text-blue-600 rounded mt-0.5" required>
                    <span class="ml-3 text-sm text-gray-600 dark:text-gray-300"><span class="text-red-500">*</span> <?= Lang::get('authorization_label') ?></span>
                </label>
            </div>
            <div class="flex items-start pt-0.5">
                <button id="submit-button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 inline-flex items-center justify-center cursor-pointer" type="submit">
                    <svg id="spinner" aria-hidden="true" class="hidden w-4 h-4 mr-2 text-gray-200 animate-spin dark:text-blue-900 fill-blue-600 dark:fill-white" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                    <span id="button-text"><?= Lang::get('submit') ?></span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Form validation and initialization scripts -->
<script src="/assets/js/form-validation.js"></script>
<script src="/assets/js/form-init.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const translations = {
        validation_email_invalid:     '<?= Lang::get('validation_email_invalid') ?>',
        validation_phone_invalid:     '<?= Lang::get('validation_phone_invalid') ?>',
        validation_max_files:         '<?= Lang::get('validation_max_files') ?>',
        validation_files_word:        '<?= Lang::get('validation_files_word') ?>',
        validation_file_type_error:   '<?= Lang::get('validation_file_type_error') ?>',
        validation_file_size_error:   '<?= Lang::get('validation_file_size_error') ?>',
        validation_file_size_exceeds: '<?= Lang::get('validation_file_size_exceeds') ?>',
        loading_title:                '<?= Lang::get('loading_title') ?>',
        loading_message:              '<?= Lang::get('loading_message') ?>',
        loading_wait:                 '<?= Lang::get('loading_wait') ?>'
    };
    
    FormInitModule.init(translations);
    
    <?php if (isset($status) && $status['success']) : ?>
    FormInitModule.resetForm();
    <?php endif; ?>
});
</script>
