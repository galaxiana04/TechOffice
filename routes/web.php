<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\ProkerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\MindMapController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\AiCustomController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DailyNotificationController;
use App\Http\Controllers\KatalogKomatController;
use App\Http\Controllers\TelegramMessagesAccountController;
use App\Http\Controllers\PDFEditorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobticketController;
use App\Http\Controllers\ZoomController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\WagroupnumberController;
use App\Http\Controllers\CriticalPartController;
use App\Http\Controllers\EkspedisiController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\NewBOMController;
use App\Http\Controllers\JustiMemoController;
use App\Http\Controllers\NewMemoController;
use App\Http\Controllers\OtomasiController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HazardLogController;
use App\Http\Controllers\NewreportController;
use App\Http\Controllers\BotTelegramController;
use App\Http\Controllers\ProjectTypeController;
use App\Http\Controllers\RamsDocumentController;
use App\Http\Controllers\NewprogressreportController;
use App\Http\Controllers\FileManagementController;
use App\Http\Controllers\HumanHourController;
use App\Http\Controllers\NotulenController;
use App\Http\Controllers\InnovationProgressController;
use App\Http\Controllers\RollingstockSpecController;
use App\Http\Controllers\ProductBreakdownStructureController;
use App\Http\Controllers\MeetingRoomController;
use App\Http\Controllers\TackController;
use App\Http\Controllers\GpsController;
use App\Http\Controllers\MemoSekdivController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\KomatProcessController;
use App\Http\Controllers\KatalogKomatChatController;
use App\Http\Controllers\FMECAController;
use App\Http\Controllers\FtaController;
use App\Http\Controllers\NewRbdController;
use App\Http\Controllers\WeibullController;
use App\Http\Controllers\MonitoringUnitController;
use App\Http\Controllers\MonitoringDokumenController;
use App\Http\Controllers\MonitoringUserController;

/// --------- Monitor -------
// API untuk data dashboard (hanya statistik)
Route::get('/newreports/level-data/{project}', [NewreportController::class, 'indexlevelData'])->name('newreports.indexlevel.data');

// versi standalone
// Route khusus dashboard standalone (fullscreen, tanpa layout)
Route::get('/newreports/level/monitor', [NewreportController::class, 'indexlevelmonitor'])->name('newreports.monitor');


Route::middleware('auth')->group(function () {

    // --------- Auth -------
    // --------- Auth -------
    // --------- Auth -------
    Route::post('/reset-password/post', [AuthController::class, 'resetPassword'])->name('auth.resetpassword');
    Route::get('/reset-password', [AuthController::class, 'ResetForm']);
    Route::get('/users/profile/{userId}', [AuthController::class, 'getUserLogs'])->name('user.logs');
    Route::get('/users', [AuthController::class, 'showAllUsers'])->name('all-users');
    Route::post('/set-internalon', [AuthController::class, 'setInternalOn'])->name('set.internalon');
    Route::post('/set-internalof', [AuthController::class, 'setInternalOff'])->name('set.internaloff');
    Route::put('/update-role/{user}', [AuthController::class, 'updateRole'])->name('update-role');
    Route::delete('/delete-user/{user}', [AuthController::class, 'deleteUser'])->name('delete-user');
    Route::get('/update-informasi', [AuthController::class, 'showUpdateForm'])->name('updateInformasiForm');
    Route::put('/update-informasi', [AuthController::class, 'updateInformasi'])->name('updateInformasi');
    Route::put('/update-password', [AuthController::class, 'updatePassword'])->name('updatePassword');
    Route::post('/update-ttd', [AuthController::class, 'updatettd'])->name('updatettd');

    // ---------- Program berjalan -------
    // ---------- Program berjalan -------
    // ---------- Program berjalan -------
    // ---------- Program berjalan -------

    // Rute untuk New RBD
    Route::prefix('newrbd')->group(function () {
        // Delete Route (POST untuk aksi destruktif)
        Route::delete('/newrbdmodels/{id}/delete', [NewRbdController::class, 'modeldestroy'])->name('newrbd.modelsdestroy');
        Route::get('/newrbdmodels/json/createmodel', [NewRbdController::class, 'jsoncreatemodelview'])->name('newrbd.jsoncreatemodelview');
        Route::post('/newrbdmodels/json/createmodel', [NewRbdController::class, 'jsoncreatemodel'])->name('newrbd.jsoncreatemodel');
        Route::get('/newrbdmodels/{id}/jsonshowmodel', [NewRbdController::class, 'jsonshowmodel'])->name('newrbd.jsonshowmodel');
        Route::post('/newrbdmodels/{id}/duplicate', [NewRbdController::class, 'duplicateModel'])->name('newrbd.duplicate');
        Route::get('/', [NewRbdController::class, 'newrbdmodelindex'])->name('newrbd.index');
        Route::get('/{id}', [NewRbdController::class, 'newrbdinstances'])->name('newrbd.newrbdinstances');
        Route::delete('/newrbdinstances/{id}', [NewRbdController::class, 'destroy'])->name('newrbd.destroy');
        Route::post('/newrbdinstances/{id}/calculate', [NewRbdController::class, 'calculate'])->name('newrbd.calculate');
        Route::post('/newrbdinstances/{id}/failurerate-calculate', [NewRbdController::class, 'failureratecalculateAndSendToPython'])->name('newrbd.failureratecalculateAndSendToPython');
        Route::post('/newrbdinstances', [NewRbdController::class, 'store'])->name('newrbd.store');
        Route::post('/newrbdinstances/failure-rates', [NewRbdController::class, 'storeFailureRate'])->name('newrbd.failure-rates.store');
        Route::get('/newrbdinstances/{id}', [NewRbdController::class, 'show'])->name('newrbd.show');
        Route::get('/newrbdinstances/ujicoba', [NewRbdController::class, 'ujicoba'])->name('newrbd.ujicoba');
        Route::post('/newrbdinstances/update-node-positions', [NewRbdController::class, 'updateNodePositions'])->name('newrbd.updateNodePositions');
        Route::post('/newrbdinstances/calculate-reliability', [NewRbdController::class, 'calculateReliability'])->name('newrbd.calculateReliability');
        Route::get('/newrbdinstances/{id}/nodes/edit', [NewRbdController::class, 'editNodes'])->name('newrbd.nodes.edit');
        Route::put('/newrbdinstances/{id}/nodes', [NewRbdController::class, 'updateNodes'])->name('newrbd.nodes.update');
        Route::get('/newrbdinstances/{id}/links/edit', [NewRbdController::class, 'editLinks'])->name('newrbd.links.edit');
        Route::put('/newrbdinstances/{id}/links', [NewRbdController::class, 'updateLinks'])->name('newrbd.links.update');
    });
    // Rute untuk Komat Process History
    Route::prefix('komatprocesshistory')->group(function () {
        Route::get('/search-suppliers', [KomatProcessController::class, 'searchSuppliers'])->name('komatprocesshistory.searchSuppliers');
        Route::post('/add-supplier', [KomatProcessController::class, 'addSupplier'])->name('komatprocesshistory.addSupplier');
        Route::get('/showuploaddoc', [KomatProcessController::class, 'showUploadForm'])->name('komatprocesshistory.showuploaddoc');
        Route::post('/uploaddoc', [KomatProcessController::class, 'uploadDocLogistik'])->name('komatprocesshistory.uploaddoc');
        Route::patch('show/{id}/close', [KomatProcessController::class, 'close'])->name('komatprocesshistory.close');
        Route::get('/show/{id}', [KomatProcessController::class, 'showDocument'])->name('komatprocesshistory.show');
        Route::post('/show/{id}/{level}', [KomatProcessController::class, 'copyTo'])->name('komatprocesshistory.copyTo');
        Route::get('/show/{id}/edit', [KomatProcessController::class, 'komatprocesshistoryedit'])->name('komatprocesshistory.edit');
        // Rute untuk update posisi unit (discussion level) di KomatProcessHistory
        Route::put('/show/{id}/updatePositions', [KomatProcessController::class, 'updatePositions'])
            ->name('komatprocesshistory.updatePositions');
        Route::get('/', [KomatProcessController::class, 'index'])->name('komatprocesshistory.index');
        // New route for adding comments
        Route::post('/show/{id}/discussion/{komatHistReqId}/{unitId}', [KomatProcessController::class, 'addComment'])
            ->name('komatprocesshistory.addComment');
        Route::put('/show/{id}/discussion/{komatHistReqId}/{unitId}/{feedbackId}/promote/{level}', [KomatProcessController::class, 'promoteFeedbackStatus'])
            ->name('komatprocesshistory.promoteFeedback');
        Route::delete('/show/{id}/discussion/{komatHistReqId}/{unitId}/{feedbackId}/delete/{level}', [KomatProcessController::class, 'deleteFeedback'])
            ->name('komatprocesshistory.deleteFeedback');
        Route::post('/show/{id}/resume/{komatHistReqId}/{unitId}', [KomatProcessController::class, 'addResumeFeedback'])
            ->name('komatprocesshistory.addResumeFeedback');
        Route::post('/show/{id}/mtpr/{komatHistReqId}/{unitId}', [KomatProcessController::class, 'addMTPRFeedback'])
            ->name('komatprocesshistory.addMTPRFeedback');
        Route::post('/add-requirement', [KomatProcessController::class, 'addRequirement'])->name('komatprocesshistory.addRequirement');
        Route::post('/referencerelation', [KomatProcessController::class, 'referencerelation'])->name('komatprocesshistory.referencerelation');
    });
    // Rute untuk inventories
    Route::prefix('inventories')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('inventories.index');
        Route::get('/data', [InventoryController::class, 'getInventories'])->name('inventories.data');
        Route::post('/store', [InventoryController::class, 'store'])->name('inventories.store');
        Route::post('/kinds/store', [InventoryController::class, 'storeKind'])->name('inventories.kinds.store');
        Route::post('/{inventory}/borrow', [InventoryController::class, 'borrow'])->name('inventories.borrow');
        Route::get('/{inventory}/loans', [InventoryController::class, 'showLoans'])->name('inventories.loans');
        Route::post('/{inventory}/update-assetcode', [InventoryController::class, 'updateAssetcode'])->name('inventories.update-assetcode');
        Route::post('/{inventory}/update-machinecode', [InventoryController::class, 'updateMachinecode'])->name('inventories.update-machinecode');
        Route::post('/loans/{loan}/return', [InventoryController::class, 'returnLoan'])->name('inventories.return');
    });
    // Rute untuk humanhour
    Route::prefix('humanhour')->group(function () {
        Route::post('/uploadsistemku/', [HumanHourController::class, 'importExcelsistem'])->name('humanhour.updateexcel');
        Route::get('/upload', [HumanHourController::class, 'upload'])->name('humanhour.upload');
        Route::get('/chart', [HumanHourController::class, 'chart'])->name('humanhour.chart');
        Route::get('/workload', [HumanHourController::class, 'getWorkloadProject'])->name('humanhour.workloadproject');
        Route::get('/hasil/chart', [HumanHourController::class, 'getChartData'])->name('humanchart.hasil.chart');
    });
    // Rute untuk Library
    Route::prefix('library')->group(function () {
        // Route GET untuk menampilkan semua file
        Route::get('/', [FileManagementController::class, 'index'])->name('library.index');

        // Route GET untuk menampilkan form unggah file baru
        Route::get('/create', [FileManagementController::class, 'create'])->name('library.create');

        // Route POST untuk menyimpan file baru
        Route::post('/', [FileManagementController::class, 'store'])->name('library.store');

        // Route GET untuk menampilkan form edit file berdasarkan ID
        Route::get('/{id}/edit', [FileManagementController::class, 'edit'])->name('library.edit');

        // Route POST untuk memperbarui file yang ada
        Route::put('/{id}', [FileManagementController::class, 'update'])->name('library.update');

        // Route POST untuk menghapus file berdasarkan ID
        Route::delete('/{id}/delete', [FileManagementController::class, 'destroy'])->name('library.destroy');
    });
    // Rute untuk Katalog Komat
    Route::prefix('katalogkomat')->group(function () {
        Route::post('/uploadsistemku', [KatalogKomatController::class, 'importExcelsistem'])->name('katalogkomat.excel');
        Route::get('/uploadexcel', [KatalogKomatController::class, 'showUploadForm'])->name('katalogkomat.formexcel');
        Route::get('/', [KatalogKomatController::class, 'index'])->name('katalogkomat.index');
        Route::get('/data', [KatalogKomatController::class, 'getData'])->name('katalogkomat.getData');
        Route::get('/chatyourkomat', [KatalogKomatChatController::class, 'index'])->name('chat.index');
        Route::get('/chatyourkomat/new', [KatalogKomatChatController::class, 'newSession'])->name('chat.new');
        Route::get('/chatyourkomat/switch/{sessionId}', [KatalogKomatChatController::class, 'switchSession'])->name('chat.switch');
        Route::post('/chatyourkomat/send', [KatalogKomatChatController::class, 'send'])->name('chat.send');
    });
    // Rute untuk Newbom
    Route::prefix('newboms')->group(function () {
        Route::get('/', [NewBOMController::class, 'indexNewbom'])->name('newbom.index');
        Route::post('/', [NewBOMController::class, 'storeNewbom']);
        Route::post('/add-requirement', [NewbomController::class, 'addRequirement']);
        Route::post('/remove-requirement', [NewbomController::class, 'removeRequirement']);

        Route::get('/show/{id}', [NewBOMController::class, 'showNewbom'])->name('newbom.show');
        Route::post('/download/{id}', [NewBomController::class, 'downloadbom'])->name('newbom.downloadbom');
        Route::get('/data', [NewbomController::class, 'data'])->name('newbom.data');
        Route::post('/delete-multiple', [NewbomController::class, 'deleteMultiple'])->name('newbom.delete.multiple');
        Route::delete('/show/{id}', [NewBOMController::class, 'destroyNewbom']);
        Route::get('/uploadexcel', [NewBOMController::class, 'showUploadForm'])->name('uploadnewbom.form');
        Route::post('/exportexcel', [NewBOMController::class, 'importExcelsistem'])->name('importnewbom.excel');
        Route::get('/logpercentage', [NewBOMController::class, 'indexlogpercentage'])->name('newboms.indexlogpercentage');
        Route::get('/create', [NewBOMController::class, 'createNewbom'])->name('newbom.create');
        Route::get('/newbomkomats/{id}', [NewBOMController::class, 'storeNewbomkomat']);
        Route::post('/newbomkomats/{id}/{idkomat}', [NewBOMController::class, 'changeNewbomkomat']);
        Route::post('/newbomkomats/{id}/{idkomat}/delete', [NewBOMController::class, 'deleteNewbomkomat']);

        Route::post('/operatorfindbykomat', [NewBOMController::class, 'operatorfindbykomat'])->name('newbom.operatorfindbykomat');
        Route::get('/search', [NewBOMController::class, 'searchkomat'])->name('newbom.searchkomat');
        Route::post('/add-new-requirement-type', [NewBomController::class, 'addNewRequirementType'])->name('newboms.add-new-requirement-type');
        Route::post('/update-komatstatus', [NewBOMController::class, 'updateKomatStatus'])->name('newbom.updatekomatstatus');
        Route::get('/newbomkomat/history/{id}', [NewbomController::class, 'datashowrev'])->name('newbomkomat.history');
    });
    // Rute untuk Nodokumen
    Route::prefix('new-memo')->group(function () {

        Route::get('/rams', [NewMemoController::class, 'listRamsDocs'])->name('new-memo.ramsindex');

        Route::get('/terbuka', [NewMemoController::class, 'indexterbuka'])->name('new-memo.index');
        Route::get('/tertutup', [NewMemoController::class, 'indextertutup'])->name('new-memo.indextertutup');
        Route::get('/roadmap/{memoId}', [NewMemoController::class, 'roadmap'])->name('new-memo.roadmap');
        Route::get('/lastfile/{memoId}', [NewMemoController::class, 'downloadfilesfromlastfeedback'])->name('new-memo.downloadfilesfromlastfeedback');
        Route::get('/timelinetracking/{memoId}', [NewMemoController::class, 'timelinetracking'])->name('new-memo.timelinetracking');


        Route::put('/show/{memoId}', [NewMemoController::class, 'updateinformasimemo'])->name('new-memo.posteditdocument');
        Route::put('/show/{memoId}/postchooseoperator', [NewMemoController::class, 'updateOperator'])->name('new-memo.postchooseoperator');
        Route::get('/show/{memoId}/edit', [NewMemoController::class, 'memoedit'])->name('new-memo.edit');
        Route::get('/show/{memoId}/chooseoperator', [NewMemoController::class, 'chooseOperator'])->name('new-memo.chooseoperator');

        Route::get('/show/{memoId}', [NewMemoController::class, 'showDocument'])->name('new-memo.show');
        Route::post('/show/{memoId}/feedback', [NewMemoController::class, 'addFeedback'])->name('new-memo.addFeedback');
        Route::post('/show/{memoId}/komat', [NewMemoController::class, 'addKomat'])->name('new-memo.addKomat');
        Route::get('/show/{memoId}/uploadsignature', [NewMemoController::class, 'documentsignature'])->name('new-memo.uploadsignature');
        Route::put('/show/{memoId}/uploadsignature', [NewMemoController::class, 'uploadsignaturefeedbackmerge'])->name('new-memo.allfeedback');
        Route::put('/show/{memoId}/uploadcombine', [NewMemoController::class, 'uploadsignaturefeedbackmerge'])->name('new-memo.Combine');
        Route::get('/show/{memoId}/uploadfeedback', [NewMemoController::class, 'documentfeedback'])->name('new-memo.uploadfeedback');
        Route::put('/show/{memoId}/sendfeedback', [NewMemoController::class, 'uploadsignaturefeedbackmerge'])->name('new-memo.sendfeedback');
        Route::put('/show/{memoId}/senddecision', [NewMemoController::class, 'sendDecision'])->name('new-memo.senddecision');
        Route::put('/show/{memoId}/sendfowardDocument', [NewMemoController::class, 'sendfowardDocument'])->name('new-memo.sendfoward');
        Route::put('/show/{memoId}/deletedfeedbackdecision', [NewMemoController::class, 'deletedFeedbackDecision'])->name('new-memo.deletedfeedbackdecision');
        Route::get('/show/{memoId}/uploadmanagerfeedback', [NewMemoController::class, 'documentmanagerfeedback'])->name('new-memo.uploadmanagerfeedback');
        Route::get('/show/{memoId}/uploadcombine', [NewMemoController::class, 'documentcombine'])->name('new-memo.uploadcombine');
        Route::put('/show/{memoId}/unsenddecision', [NewMemoController::class, 'unsendDecision'])->name('new-memo.unsenddecision');
        Route::post('/show/{memoId}/updatedocumentstatus/', [NewMemoController::class, 'updateStatus'])->name('new-memo.updatedocumentstatus');

        Route::post('/upload', [NewMemoController::class, 'uploadDocMTPRLogistik'])->name('new-memo.upload');
        Route::get('/upload', [NewMemoController::class, 'uploadForm']);

        Route::get('/migrasimemonewmemo', [NewMemoController::class, 'migrasimemonewmemo']);
        Route::get('/migrasimemonewmemoefesien', [NewMemoController::class, 'migrasimemonewmemoefesien']);
        Route::get('/migrasimemonewmemoefesienbyproject', [NewMemoController::class, 'migrasimemonewmemoefesienbyproject']);

        Route::get('/show/all/leadtimeperunit', [NewMemoController::class, 'leadtimeperunit']);

        Route::get('/indexterbukayajra', [NewMemoController::class, 'indexterbukayajra'])->name('new-memo.indexterbukayajra');

        Route::post('/download', [NewMemoController::class, 'newmemodownload'])->name('new-memo.download');
        Route::get('/downloadall', [NewMemoController::class, 'newmemodownloadall'])->name('new-memo.downloadall');
        Route::get('/monitoring-unit', [NewMemoController::class, 'indexmonitoring'])->name('new-memo.monitoring.unit');
        Route::get('/monitoring-unit', [MonitoringUnitController::class, 'index'])->name('new-memo.monitoring.unit');
    });
    // Monitoring Unit
    Route::get('/monitoring-unit', [MonitoringUnitController::class, 'index'])->name('monitoring.unit');
    Route::get('/monitoring-unit/detail', [MonitoringUnitController::class, 'getUnitDetail'])->name('monitoring.unit.detail');

    // Monitoring User
    Route::get('/monitoring-user', [MonitoringUserController::class, 'index'])->name('monitoring.user');
    Route::get('/monitoring-user/{id}', [MonitoringUserController::class, 'show']);
    

    //Ruang Rapat
    Route::prefix('events')->group(function () {
        Route::get('/all', [EventController::class, 'index'])->name('events.all');

        Route::get('/rooms/{room}', [EventController::class, 'room'])->name('events.room');
        Route::get('/edit/{id}', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/update/{id}', [EventController::class, 'update'])->name('events.update');
        Route::get('/create', [EventController::class, 'create'])->name('events.create');
        Route::get('/listMeetingParticipants/{id}', [EventController::class, 'listMeetingParticipants'])->name('events.listMeetingParticipants');


        Route::post('/check/roomavailability', [EventController::class, 'checkRoomAvailability'])->name('checkRoomAvailability');
    });
    //Ruang Rapat
    Route::prefix('jobticket')->group(function () {
        // jobticket update
        Route::get('/closedJobTicket', [JobticketController::class, 'closedJobTicket']);
        Route::put('/jobticket-identity/{id}/update-documentname', [JobticketController::class, 'updateDocumentName'])->name('jobticket.updateDocumentName');
        Route::put('/jobticket-identity/{id}/update-documentnumber', [JobticketController::class, 'updateDocumentNumber'])->name('jobticket.updateDocumentNumber');


        Route::post('/downloadzip', [JobticketController::class, 'downloadjobticket'])
            ->name('jobticket.downloadZIP');

        Route::post('/downloadexcel', [JobticketController::class, 'downloadexcel'])
            ->name('jobticket.downloadexcel');

        Route::get('/downloadWLA', [JobticketController::class, 'downloadWLA'])->name('jobticket.downloadWLA');

        Route::get('/unit', [JobticketController::class, 'showunit'])->name('jobticket.showunit');



        Route::get('/jobticket-document-kind', [JobticketController::class, 'indexjobticketdokumentkind'])->name('jobticket.jobticket-document-kindindex');
        Route::post('/jobticket-document-kind', [JobticketController::class, 'storejobticketdokumentkind'])->name('jobticket.jobticket-document-kindstore');

        Route::post('/jobticket-released/{id}', [JobticketController::class, 'releasedDocument'])->name('jobticket.released');

        Route::get('/', [JobticketController::class, 'index'])->name('jobticket.index');
        Route::get('/show/{id}', [JobticketController::class, 'show'])->name('jobticket.show');
        Route::get('/show/{id}/{iddocumentnumber}', [JobticketController::class, 'showdocument'])->name('jobticket.showdocument');
        Route::put('/updatesupportdocument/{jobticketid}', [JobticketController::class, 'updatesupportdocument'])->name('jobticket.updatesupportdocument');

        Route::get('/showself/terbuka', [JobticketController::class, 'showdocumentselfterbuka'])->name('jobticket.showdocumentselfterbuka');
        Route::get('/showself/tertutup', [JobticketController::class, 'showdocumentselftertutup'])->name('jobticket.showdocumentselftertutup');

        Route::get('/manager/terbuka', [JobticketController::class, 'managershow'])->name('jobticket.managershow');

        Route::get('/showmember/{id}/{status}', [JobticketController::class, 'showdocumentmember'])->name('jobticket.showdocumentmember');

        Route::get('/show/{jobticket_identity_part}/{jobticket_identity_id}/{jobticket_id}', [JobticketController::class, 'detail'])->name('jobticket.detail');


        Route::post('/statushistory/{id}/mark-as-read', [JobticketController::class, 'markAsRead'])->name('jobticket.markAsRead');


        Route::get('/deletejobticket/{jobticket_identity_part}/{jobticket_identity_id}/{jobticket_id}', [JobticketController::class, 'deletejobticket'])->name('jobticket.deletejobticket');

        Route::get('/close/{jobticket_identity_part}/{jobticket_identity_id}/{jobticket_id}', [JobticketController::class, 'close'])->name('jobticket.close');
        Route::get('/uploadexcel', [JobticketController::class, 'showUploadFormExcel'])->name('jobticket.uploadexcel');
        Route::post('/uploadsistemku/', [JobticketController::class, 'importExcelsistem'])->name('jobticket.updateexcel');

        Route::post('/picknote/{id}', [JobticketController::class, 'picknote'])->name('jobticket.picknote');
        Route::post('/pickdraftercheckerapprover/{id}', [JobticketController::class, 'pickdraftercheckerapprover'])->name('jobticket.pickdraftercheckerapprover');
        Route::post('/jobticketstartedrev/pickdraftercheckerapprover/{id}', [JobticketController::class, 'jobticketstartedrevpickdraftercheckerapprover'])->name('jobticket.jobticketstartedrevpickdraftercheckerapprover');

        Route::put('/update-revision/{id}', [JobticketController::class, 'updateRevision']);

        Route::post('/picktugas/{id}/{name}/{kindposition}', [JobticketController::class, 'picktugas'])->name('jobticket.picktugas');
        Route::post('/starttugas/{id}/{name}', [JobticketController::class, 'starttugas'])->name('jobticket.starttugas');
        Route::post('/pausetugas/{id}/{name}', [JobticketController::class, 'pausetugas'])->name('jobticket.pausetugas');

        Route::post('/reasontugas/', [JobticketController::class, 'reasontugas'])->name('jobticket.reasontugas');

        Route::post('/resumetugas/{id}/{name}', [JobticketController::class, 'resumetugas'])->name('jobticket.resumetugas');
        Route::post('/selesaitugas/{id}/{name}', [JobticketController::class, 'selesaitugas'])->name('jobticket.selesaitugas');

        Route::post('/izinkanrevisitugas/{id}/{name}', [JobticketController::class, 'izinkanrevisitugas'])->name('jobticket.izinkanrevisitugas');
        Route::post('/approveperbaikan/{revision}/{kindposition}', [JobticketController::class, 'revisionapprove'])->name('jobticket.revisionapprove');


        Route::post('/reminder/{revision}/{kindposition}', [JobticketController::class, 'reminder'])->name('jobticket.reminder');



        Route::put('/revisionapprovedoc/{revision}/{kindposition}', [JobticketController::class, 'revisionapprovedoc'])->name('jobticket.revisionapprovedoc');
        Route::get('/uploadverifikasi/{revision}', [JobticketController::class, 'uploadverifikasi'])->name('jobticket.uploadverifikasi');
        Route::delete('/deleterevision/{revision}', [JobticketController::class, 'deleteRevision'])->name('jobticket.deleterevision');

        Route::get('/rank', [JobticketController::class, 'rank'])->name('jobticket.rank');

        Route::get('/unfinished', [JobticketController::class, 'unfinished'])->name('jobticket.unfinished');

        Route::post('/jobticket-add-document', [JobticketController::class, 'AddDocument'])->name('jobticket.AddDocument');
    });
    //Rute untuk progress dokumen
    Route::prefix('newreports')->group(function () {
        // Route untuk menampilkan halaman form dinamis
        Route::get('/progress/dynamic-create', [NewprogressreportController::class, 'createDynamic'])
            ->name('newprogressreports.create_dynamic');
        // Route untuk handle POST simpan (Tanpa parameter {newreport} di URL)
        Route::post('/progress/store-dynamic', [NewprogressreportController::class, 'storeDynamic'])
            ->name('newprogressreports.store_dynamic');
        // Route download draft
        Route::post('/progress/download-draft', [NewprogressreportController::class, 'downloadExcelDraft'])
            ->name('newprogressreports.download_draft');
        Route::get('/monitoring-dokumen', [MonitoringDokumenController::class, 'index'])->name('monitoring.dokumen');
        Route::get('/level', [NewreportController::class, 'indexlevel'])->name('newreports.indexlevel');
        Route::get('/level/{project_id}/{level_id}', [NewreportController::class, 'showlevel'])->name('level2.show');
        Route::get('/old', [NewreportController::class, 'index'])->name('newreports.indexold');
        Route::get('/slideshow', [NewreportController::class, 'indexslideshow'])->name('newreports.indexslideshow');
        Route::get('/', [NewreportController::class, 'indexperproject'])->name('newreports.index');
        Route::post('/dashboard/data', [NewreportController::class, 'getDashboardData'])->name('newreports.dashboarddata');
        Route::get('/{newreport}/doubledetector', [NewreportController::class, 'doubledetector'])->name('newreports.doubledetector');
        Route::post('/{newreport}/download', [NewreportController::class, 'downloadprogress'])->name('newreports.download');
        Route::post('/{project}/downloadbyproject', [NewreportController::class, 'downloadprogressbyproject'])->name('newreports.downloadbyproject');
        Route::get('/{newreport}/viewbyprojectprogress', [NewreportController::class, 'viewbyprojectprogress'])->name('newreports.viewbyprojectprogress');
        Route::post('/{newreport}/downloadlaporan', [NewreportController::class, 'downloadlaporan'])->name('newreports.downloadlaporan');
        Route::get('/{newreport}/downloadlaporan', [NewreportController::class, 'downloadlaporan'])->name('newreports.downloadlaporanGet');
        Route::post('/newreportsdownloadlaporanall/{project}', [NewreportController::class, 'downloadlaporanall'])->name('newreports.downloadlaporanall');
        Route::post('/newreportsdownloadlaporanallrevnol/{project}', [NewreportController::class, 'downloadlaporanallrevnol'])->name('newreports.downloadlaporanallrevnol');
        Route::post('/{newreport}/downloadduplicatebyproject', [NewreportController::class, 'downloadduplicatebyproject'])->name('newreports.downloadduplicatebyproject');
        Route::delete('/{newreport}/destroy', [NewreportController::class, 'destroy'])->name('newreports.destroy');
        Route::delete('/{newreport}/destroydian', [NewreportController::class, 'destroydian'])->name('newreports.destroydian');
        Route::get('/{newreport}/progressreports/create', [NewprogressreportController::class, 'create'])->name('newprogressreports.create');
        Route::post('/{newreport}/progressreports', [NewprogressreportController::class, 'store'])->name('newprogressreports.store');
        Route::post('/update-documentnumber', [NewreportController::class, 'updateDocumentNumber'])->name('newreports.updateDocumentNumber');
        Route::get('/calculatelastpercentage', [NewreportController::class, 'calculatelastpercentage'])->name('newreports.calculatelastpercentage');
        Route::get('/indexlogpercentage', [NewreportController::class, 'indexlogpercentage'])->name('newreports.indexlogpercentage');
        Route::get('/{newreport}/showlog/{logid}', [NewreportController::class, 'showlog'])->name('newreports.showlog');
        Route::get('/{newreport}', [NewreportController::class, 'show'])->name('newreports.show');
        Route::get('/{newreport}/{id}', [NewreportController::class, 'showrev'])->name('newreports.showrev');
        Route::get('/{newreport}/{id}/pdf', [NewreportController::class, 'lastpdffile'])->name('newreports.lastpdffile');
        Route::get('/newprogressreports/lasthistory/{id}/pdf', [NewreportController::class, 'lastpdffilerev'])->name('newreports.lastpdffilerev');
        Route::get('/laporan/{newreport}', [NewreportController::class, 'laporan'])->name('newreports.laporan');


    });
    Route::prefix('newprogressreports')->group(function () {
        // routes/web.php
        Route::post(
            '/document-kind/{kind}/assign-unit',
            [NewprogressreportController::class, 'assignUnit']
        )->name('newprogressreports.document-kind-assign-unit');
        Route::post('/update-document-kind-progress', [NewprogressreportController::class, 'updateDocumentKind'])
            ->name('newprogressreports.updateDocumentKind');



        Route::post('/{newprogressreport}/delete', [NewprogressreportController::class, 'destroy']);
        Route::get('/{newprogressreport}/detail', [NewprogressreportController::class, 'detail']);
        Route::post('/picktugas/{id}/{name}', [NewprogressreportController::class, 'picktugas'])->name('newprogressreports.picktugas');
        Route::post('/starttugas/{id}/{name}', [NewprogressreportController::class, 'starttugas'])->name('newprogressreports.starttugas');
        Route::post('/pausetugas/{id}/{name}', [NewprogressreportController::class, 'pausetugas'])->name('newprogressreports.pausetugas');
        Route::post('/resumetugas/{id}/{name}', [NewprogressreportController::class, 'resumetugas'])->name('newprogressreports.resumetugas');
        Route::post('/selesaitugas/{id}/{name}', [NewprogressreportController::class, 'selesaitugas'])->name('newprogressreports.selesaitugas');
        Route::post('/resettugas/{id}/{name}', [NewprogressreportController::class, 'resettugas'])->name('newprogressreports.resettugas');
        Route::post('/unlinkparent/{id}/', [NewprogressreportController::class, 'unlinkparent'])->name('newprogressreports.unlinkparent');
        Route::post('/izinkanrevisitugas/{id}/{name}', [NewprogressreportController::class, 'izinkanrevisitugas'])->name('newprogressreports.izinkanrevisitugas');
        Route::post('/updateprogressreport/{id}/', [NewprogressreportController::class, 'updateprogressreport'])->name('newprogressreports.updateprogressreport');
        Route::get('/document-kind', [NewprogressreportController::class, 'indexdokumentkind'])->name('newprogressreports.document-kindindex');
        Route::post('/document-kind', [NewprogressreportController::class, 'storedokumentkind'])->name('newprogressreports.document-kindstore');
        Route::get('/search', [NewProgressReportController::class, 'showSearchForm'])->name('newprogressreports.searchform');
        Route::get('/search/results', [NewProgressReportController::class, 'search'])->name('newprogressreports.search');
        Route::post('/newprogressreports/uploadsistemku/', [NewprogressreportController::class, 'importExcelsistem'])->name('newprogressreports.updateexcel');
        Route::get('/upload', [NewprogressreportController::class, 'showUploadFormExcel']);
        Route::post('/handleDeleteMultipleItems', [NewprogressreportController::class, 'handleDeleteMultipleItems'])->name('newprogressreports.handleDeleteMultipleItems');

        Route::post('/handleReleaseMultipleItems', [NewprogressreportController::class, 'handleReleaseMultipleItems'])->name('newprogressreports.handleReleaseMultipleItems');
        Route::post('/handleUnreleaseMultipleItems', [NewprogressreportController::class, 'handleUnreleaseMultipleItems'])->name('newprogressreports.handleUnreleaseMultipleItems');
        Route::get('/notif-harian-units', [NewprogressreportController::class, 'indexnotifharian'])->name('newprogressreports.index-notif-harian-units');
        Route::post('/notif-harian-units', [NewprogressreportController::class, 'storenotifharian'])->name('newprogressreports.store-notif-harian-units');
        Route::get('/notif-harian-units/{id}/edit', [NewprogressreportController::class, 'editnotifharian'])->name('newprogressreports.edit-notif-harian-unit');
        Route::post('/notif-harian-units/{id}', [NewprogressreportController::class, 'updatenotifharian'])->name('newprogressreports.update-notif-harian-unit');
        Route::delete('/notif-harian-units/{id}', [NewprogressreportController::class, 'deletenotifharian'])->name('newprogressreports.delete-notif-harian-unit');

        Route::get('/today', [NewprogressreportController::class, 'today']);
    });



    // routes/web.php atau di dalam group yang sudah ada
    Route::prefix('failurerate')->group(function () {
        Route::get('/uploadsistemku', [WeibullController::class, 'showUploadFormExcel'])->name('weibull.showupdateexcel');
        Route::post('/uploadsistemku', [WeibullController::class, 'importExcelsistem'])->name('weibull.updateexcel');
        Route::get('/weibull-recalculate', function () {
            app(WeibullController::class)->recalculateAll();
            return "Analisis Weibull berhasil dihitung ulang untuk semua komponen!";
        });
        Route::get('/calculation-method', [WeibullController::class, 'calculationmethod'])->name('weibull.calculation-method');
        Route::get('/', [WeibullController::class, 'index'])->name('weibull.dashboard');
        Route::get('/projects', [WeibullController::class, 'projectDashboard'])->name('weibull.project-dashboard');
        Route::post('/projects', [WeibullController::class, 'storeProject'])->name('weibull.project-store');
        Route::put('/projects/{project}', [WeibullController::class, 'updateProject'])->name('weibull.project-update');
        Route::delete('/projects/{project}', [WeibullController::class, 'deleteProject'])->name('weibull.project-delete');
        Route::get('/add-failure', [WeibullController::class, 'createFailure'])->name('weibull.create');
        Route::post('/add-failure', [WeibullController::class, 'storeFailure'])->name('weibull.store');
        Route::post('/upload', [WeibullController::class, 'upload'])->name('weibull.upload');
        Route::get('/component/{component}', [WeibullController::class, 'show'])->name('weibull.detail');
    });
    // ---------- Support berjalan -------
    // ---------- Support berjalan -------
    // ---------- Support berjalan -------
    // ---------- Support berjalan -------

    // Rute untuk Unit
    Route::prefix('fta')->group(function () {
        Route::get('/', [FtaController::class, 'index'])->name('fta.index');
        Route::get('/create', [FtaController::class, 'create'])->name('fta.create');
        Route::post('/', [FtaController::class, 'store'])->name('fta.store');

        Route::get('/json', [FtaController::class, 'json'])->name('fta.json');

        Route::get('/{ftaidentity_id}', [FtaController::class, 'show'])->name('fta.show');
        Route::get('/{ftaidentity_id}/edit', [FtaController::class, 'edit'])->name('fta.edit');
        Route::put('/{ftaidentity_id}', [FtaController::class, 'update'])->name('fta.update');
        Route::delete('/{ftaidentity_id}', [FtaController::class, 'destroy'])->name('fta.destroy');

        Route::get('/{ftaidentity_id}/project', [FtaController::class, 'project'])->name('fta.project');
        Route::post('/{ftaidentity_id}/calculate', [FtaController::class, 'calculateCFIAndStore'])->name('fta.calculate');

        Route::get('/{ftaidentity_id}/nodes/edit', [FtaController::class, 'editNodes'])->name('fta.nodes.edit');
        Route::put('/{ftaidentity_id}/nodes', [FtaController::class, 'updateNodes'])->name('fta.nodes.update');

        Route::get('/{ftaidentity_id}/events/edit', [FtaController::class, 'editEvents'])->name('fta.events.edit');
        Route::put('/{ftaidentity_id}/events', [FtaController::class, 'updateEvents'])->name('fta.events.update');
    });
    // Rute untuk Project
    Route::prefix('project_types')->group(function () {
        // Menampilkan daftar project types (Index)
        Route::get('/', [ProjectTypeController::class, 'index'])->name('project_types.index');

        // Menampilkan form untuk membuat project type baru (Create)
        Route::get('/create', [ProjectTypeController::class, 'create'])->name('project_types.create');

        // Menyimpan project type baru (Store)
        Route::post('/store', [ProjectTypeController::class, 'store'])->name('project_types.store');

        Route::get('/data', [ProjectTypeController::class, 'data'])->name('project_types.data');


        // Memperbarui project type tertentu (Update)
        Route::post('/update/{project_type}', [ProjectTypeController::class, 'update'])->name('project_types.update');

        // Menghapus project type tertentu (Destroy)
        Route::post('/delete/{project_type}', [ProjectTypeController::class, 'destroy'])->name('project_types.destroy');
    });
    // Rute untuk Ekspedisi
    Route::prefix('ekspedisi')->group(function () {
        Route::get('/', [EkspedisiController::class, 'index']);
        Route::post('/upload-pdf', [EkspedisiController::class, 'sendPdfToNode'])->name('ekspedisi.upload');
    });
    // Rute untuk Telegram
    Route::prefix('telegram-messages-accounts')->group(function () {
        Route::get('/', [TelegramMessagesAccountController::class, 'index'])->name('telegram_messages_accounts.index');
        Route::post('/', [TelegramMessagesAccountController::class, 'store'])->name('telegram_messages_accounts.store');
        Route::get('/create', [TelegramMessagesAccountController::class, 'create'])->name('telegram_messages_accounts.create');
        Route::get('/show/{telegramMessagesAccount}', [TelegramMessagesAccountController::class, 'show'])->name('telegram_messages_accounts.show');
        Route::get('/edit/{telegramMessagesAccount}/edit', [TelegramMessagesAccountController::class, 'edit'])->name('telegram_messages_accounts.edit');
        Route::put('/update/{telegramMessagesAccount}', [TelegramMessagesAccountController::class, 'update'])->name('telegram_messages_accounts.update');
        Route::delete('/destroy/{telegramMessagesAccount}', [TelegramMessagesAccountController::class, 'destroy'])->name('telegram_messages_accounts.destroy');
    });
    // Rute untuk Notification
    Route::prefix('notification/')->group(function () {
        Route::get('/receive/{namadivisi}', [NotificationController::class, 'showByDivisi'])->name('notification.show');
        Route::post('/sendwa', [NotificationController::class, 'sendwa'])->name('notification.sendwa');
        Route::get('/viewsendwa', [NotificationController::class, 'viewsendwa'])->name('notification.viewsendwa');
    });
    // Rute untuk Meeting Room
    Route::resource('meetingrooms', MeetingRoomController::class);
    Route::prefix('zoom')->group(function () {
        Route::get('/', [ZoomController::class, 'index'])->name('zoom.index');
        Route::post('/', [ZoomController::class, 'store'])->name('zoom.store');
        Route::get('/create', [ZoomController::class, 'create'])->name('zoom.create');
        Route::get('/{id}', [ZoomController::class, 'show'])->name('zoom.show');
        Route::delete('/{id}', [ZoomController::class, 'destroy'])->name('zoom.destroy');
        Route::post('/{id}/update', [ZoomController::class, 'update'])->name('zoom.update');
        Route::post('/delete-multiple', [ZoomController::class, 'deleteMultiple'])->name('zoom.deleteMultiple');
        // zoom delete
        Route::get('/auth/{account_name}/zoomverify', [ZoomController::class, 'handleZoomCallback']);
        Route::get('/redirectzoom/{account_name}/', [ZoomController::class, 'redirectToZoom'])->name('zoom.auth');
        Route::get('/delete-meeting/{account_name}/{meetingId}', [ZoomController::class, 'deleteMeeting'])->name('meeting.delete.form');
        Route::delete('/delete-meeting/{account_name}/{meetingId}', [ZoomController::class, 'deleteMeeting'])->name('meeting.delete');
    });
    Route::get('/database-error', function () {
        return response()->view('errors.database', [], 503);
    })->name('database.error');



    // ---------- Program tidak berjalan -------
    // ---------- Program tidak berjalan -------
    // ---------- Program tidak berjalan -------
    // ---------- Program tidak berjalan -------

    Route::prefix('fta')->group(function () {
        Route::resource('/', FtaController::class);
        Route::get('/', [FtaController::class, 'index'])->name('fta.index');
        Route::get('/{ftaidentity_id}/project', [FtaController::class, 'project'])->name('fta.project');
        Route::post('/{ftaidentity_id}/calculate', [FtaController::class, 'calculateCFIAndStore'])->name('fta.calculate');
        Route::get('/{ftaidentity_id}/nodes/edit', [FtaController::class, 'editNodes'])->name('fta.nodes.edit');
        Route::put('/{ftaidentity_id}/nodes', [FtaController::class, 'updateNodes'])->name('fta.nodes.update');
        Route::get('/{ftaidentity_id}/events/edit', [FtaController::class, 'editEvents'])->name('fta.events.edit');
        Route::put('/{ftaidentity_id}/events', [FtaController::class, 'updateEvents'])->name('fta.events.update');
        Route::get('/json', [FtaController::class, 'json'])->name('fta.json');
    });
    Route::prefix('fmeca')->group(function () {
        Route::get('/', [FMECAController::class, 'index'])->name('fmeca.index');
        Route::post('/', [FMECAController::class, 'store'])->name('fmeca.store');
        Route::get('/items/{fmecaPart}', [FMECAController::class, 'items'])->name('fmeca.items'); // Existing AJAX route
        Route::get('/items/view/{fmecaPart}', [FMECAController::class, 'viewItems'])->name('fmeca.items.view'); // New route for view
        Route::get('/items/{fmecaItem}/delete', [FMECAController::class, 'destroy'])->name('fmeca.delete');
        Route::put('/items/{fmecaItem}', [FMECAController::class, 'update'])->name('fmeca.update');
        Route::post('/{fmecaPart}/reorder', [FMECAController::class, 'reorder'])->name('fmeca.reorder');
        Route::get('/critical-items', [FMECAController::class, 'criticalItems'])->name('fmeca.critical-items');
        Route::get('/critical-items/export', [FMECAController::class, 'exportCriticalItems'])->name('fmeca.critical-items.export');
    });
    // Rute untuk Product Breakdown Structure
    Route::prefix('product-breakdown-structure')->group(function () {
        Route::get('/', [ProductBreakdownStructureController::class, 'index'])->name('product-breakdown-structure.index');
        Route::get('/allref', [ProductBreakdownStructureController::class, 'indexallreference'])->name('product-breakdown-structure.indexallreference');
        Route::get('/data/{projectId}', [ProductBreakdownStructureController::class, 'getData'])->name('product-breakdown-structure.getData');
        Route::get('/alldata', [ProductBreakdownStructureController::class, 'getAllData'])->name('product-breakdown-structure.getAllData');
        Route::post('/attach-report', [ProductBreakdownStructureController::class, 'attachnewreportprogressandrealibiltyallocation'])->name('reliability_allocations.attach');
        Route::get('/detach-report/{reliability_allocation_id}/{newprogressreport_id}', [ProductBreakdownStructureController::class, 'detachnewreportprogressandrealibiltyallocation'])->name('reliability_allocations.detach');
        Route::post('/upload-excel', [ProductBreakdownStructureController::class, 'uploadExcel'])->name('product-breakdown-structure.upload-excel');
        Route::post('/delete-all', [ProductBreakdownStructureController::class, 'destroyAll'])->name('product-breakdown-structure.delete-all');
    });
    // Rute untuk Memo Sekdiv
    Route::prefix('memosekdivs')->group(function () {
        // Menampilkan semua memo
        Route::get('/', [MemoSekdivController::class, 'index'])->name('memosekdivs.index');
        Route::get('/create', [MemoSekdivController::class, 'create'])->name('memosekdivs.create');
        Route::get('/show/{id}', [MemoSekdivController::class, 'showDocument'])->name('memosekdivs.show');
        Route::get('/show/{id}/edit', [MemoSekdivController::class, 'memosekdivedit'])->name('memosekdivs.edit');
        Route::put('/show/{id}', [MemoSekdivController::class, 'updateinformasimemo'])->name('memosekdivs.posteditdocument');
        Route::get('/show/{id}/uploadfeedback', [MemoSekdivController::class, 'documentfeedback'])->name('memosekdivs.uploadfeedback');
        Route::get('/show/{id}/uploadmanagerfeedback', [MemoSekdivController::class, 'documentmanagerfeedback'])->name('memosekdivs.uploadmanagerfeedback');
        Route::get('/show/{id}/uploadreply', [MemoSekdivController::class, 'uploadreply'])->name('memosekdivs.uploadreply');
        Route::put('/show/{id}/uploadsignaturefeedbackmerge', [MemoSekdivController::class, 'uploadsignaturefeedbackmerge'])->name('memosekdivs.allfeedback');
        Route::put('/show/{id}/senddecision', [MemoSekdivController::class, 'sendDecision'])->name('memosekdivs.senddecision');
        Route::put('/show/{id}/sendfowardDocument', [MemoSekdivController::class, 'sendfowardDocument'])->name('memosekdivs.sendfoward');
        // Proses simpan memo baru
        Route::post('/', [MemoSekdivController::class, 'store'])->name('memosekdivs.store');
        Route::post('/access/store', [MemoSekdivController::class, 'storeAccess'])->name('memo-sekdiv-access.store');
        Route::get('/access/{documentId}/list', [MemoSekdivController::class, 'listAccess'])->name('memo-sekdiv-access.list');
    });
    // Rute untuk Tack
    Route::prefix('tack')->group(function () {


        Route::get('/', [TackController::class, 'index'])->name('tack.index');
        Route::get('/getprojectdata/{id}', [TackController::class, 'getProjectData']);
        Route::get('/projects', [TackController::class, 'getProjects']);
        Route::get('/upload', [TackController::class, 'upload'])->name('tack.upload');
        Route::post('/uploadsistemku/', [TackController::class, 'importExcelsistem'])->name('tack.updateexcel');
    });
    // Rute untuk AI Custom
    Route::prefix('aicustom')->group(function () {

        Route::get('/', [AiCustomController::class, 'index'])->name('aicustom.index');
        Route::post('/', [AiCustomController::class, 'store'])->name('aicustom.store');
        Route::get('/{aicustom}', [AiCustomController::class, 'show'])->name('aicustom.show');
        Route::put('/{aicustom}', [AiCustomController::class, 'update'])->name('aicustom.update');
        Route::delete('/{aicustom}', [AiCustomController::class, 'destroy'])->name('aicustom.destroy');
    });
    // Rute untuk Critical Part
    Route::prefix('criticalpart')->group(function () {
        Route::get('/', [CriticalPartController::class, 'index'])->name('fmecax.index'); // Menampilkan halaman utama
        Route::get('/show/{fmeca_identity_id}', [CriticalPartController::class, 'show'])->name('fmecax.show'); // Menampilkan halaman utama

        Route::get('/upload', [CriticalPartController::class, 'upload'])->name('fmecax.upload');
        Route::post('/upload-excel', [CriticalPartController::class, 'uploadexcell'])->name('fmecax.upload.excel');
        Route::get('/data', [CriticalPartController::class, 'data'])->name('fmecax.data');
        Route::post('/download/{project}', [CriticalPartController::class, 'download'])->name('fmecax.download');
        Route::delete('/{id}', [CriticalPartController::class, 'destroy'])->name('fmecax.destroy');
    });
    // Rute untuk Proker LPK
    Route::prefix('prokerlpk')->group(function () {
        Route::get('/broadcast', [ProkerController::class, 'broadcast'])->name('proker.broadcast'); // Menampilkan halaman utama
        Route::get('/prokerBroadcast/{id}', [ProkerController::class, 'prokerBroadcast'])->name('proker.prokerBroadcast'); // Menampilkan halaman utama
        Route::delete('/delete/{id}', [ProkerController::class, 'destroy'])->name('proker.destroy'); // Menampilkan halaman utama
        Route::patch('/toggle-hide/{id}', [ProkerController::class, 'toggleHide'])->name('proker.toggleHide'); // <- GANTI KE PATCH

        Route::get('/', [ProkerController::class, 'index'])->name('proker.index'); // Menampilkan halaman utama
        Route::get('/show/unit/{id}', [ProkerController::class, 'show'])->name('proker.show'); // Menampilkan halaman utama
        Route::get('/historyProker', [ProkerController::class, 'historyProker'])->name('proker.historyProker'); // Mengambil data Proker berdasarkan bulan & unit
        Route::get('/get/{id}', [ProkerController::class, 'getProker'])->name('proker.get');
        Route::get('/searchProker', [ProkerController::class, 'searchProker'])->name('proker.searchProker'); // Mengambil data Proker berdasarkan bulan & unit

        Route::post('/store', [ProkerController::class, 'store'])->name('proker.store'); // Menyimpan Proker baru
        Route::post('/store-monthly', [ProkerController::class, 'storeMonthly'])->name('proker.store-monthly'); // New route
    });
    Route::prefix('rollingstock')->group(function () {
        Route::get('/get/{id}', [RollingstockSpecController::class, 'getRollingstock'])->name('rollingstock.getRollingstock');
        Route::delete('/file/delete/{fileId}', [RollingstockSpecController::class, 'deleteFile'])->name('rollingstock.file.delete');
        Route::get('/', [RollingstockSpecController::class, 'index'])->name('rollingstock.index');
        Route::post('/store', [RollingstockSpecController::class, 'store'])->name('rollingstock.store');
        Route::put('/update/{id}', [RollingstockSpecController::class, 'update'])->name('rollingstock.update');
        Route::delete('/delete/{id}', [RollingstockSpecController::class, 'destroy'])->name('rollingstock.destroy');
    });
    // Rute untuk Innovation Progress
    Route::prefix('innovation-progress')->group(function () {
        Route::get('/', [InnovationProgressController::class, 'index'])->name('innovation_progress.index');
        Route::post('/store', [InnovationProgressController::class, 'store'])->name('innovation_progress.store');

        Route::put('/update/{id}', [InnovationProgressController::class, 'update'])->name('innovation_progress.update');

        Route::delete('/destroy/{id}', [InnovationProgressController::class, 'destroy'])->name('innovation_progress.destroy');
    });
    // Rute untuk Hazard Logs
    Route::prefix('hazard_logs')->group(function () {
        Route::get('/', [HazardLogController::class, 'index'])->name('hazard_logs.index');
        Route::post('/', [HazardLogController::class, 'store'])->name('hazard_logs.store');
        Route::get('/create', [HazardLogController::class, 'create'])->name('hazard_logs.create');
        Route::put('/{id}', [HazardLogController::class, 'update'])->name('hazard_logs.update');
        Route::delete('/{id}', [HazardLogController::class, 'destroy'])->name('hazard_logs.destroy');
        Route::get('{id}/show/', [HazardLogController::class, 'show'])->name('hazard_logs.show');
        Route::get('/{id}/edit', [HazardLogController::class, 'edit'])->name('hazard_logs.edit');
        Route::get('/{id}/{level}/feedback', [HazardLogController::class, 'viewfeedback'])->name('hazard_logs.feedback');
        Route::get('/{id}/{level}/combine', [HazardLogController::class, 'viewcombine'])->name('hazard_logs.combine');

        Route::post('/{id}/feedback', [HazardLogController::class, 'submitFeedback'])->name('hazard_logs.submitFeedback');
        Route::delete('/{hazardLogId}/feedback/{feedbackId}', [HazardLogController::class, 'destroyFeedback'])->name('hazard_logs.feedback.destroy');
        Route::post('/{hazardLogId}/approve/{feedbackId}', [HazardLogController::class, 'approveFeedback'])->name('hazard_logs.feedback.approve');
        Route::post('/{hazardLogId}/reject/{feedbackId}', [HazardLogController::class, 'rejectFeedback'])->name('hazard_logs.feedback.reject');

        Route::post('/{hazardLogId}/deletestatus/', [HazardLogController::class, 'deletestatus'])->name('hazard_logs.deletestatus');


        Route::post('/{hazardLogId}/approvehazardlog/{reductionMeasureId}', [HazardLogController::class, 'approvehazardlog'])->name('hazard_logs.hazardlog.approve');
        Route::post('/{hazardLogId}/rejecthazardlog/{reductionMeasureId}', [HazardLogController::class, 'rejecthazardlog'])->name('hazard_logs.hazardlog.reject');
        Route::post('/{hazardLogId}/addhazardlog/{unit_name}', [HazardLogController::class, 'addhazardlog'])->name('hazard_logs.hazardlog.add');
        Route::post('/{hazardLogId}/makeforum/{reductionMeasureId}', [HazardLogController::class, 'makeforum'])->name('hazard_logs.hazardlog.makeforum');
    });
    // Rute untuk RAMS
    Route::prefix('rams')->group(function () {
        Route::get('/terbuka', [RamsDocumentController::class, 'indexterbuka'])->name('ramsdocuments.indexterbuka');
        Route::get('/tertutup', [RamsDocumentController::class, 'indextertutup'])->name('ramsdocuments.indextertutup');
        Route::get('/create', [RamsDocumentController::class, 'create'])->name('ramsdocuments.create');
        Route::post('/', [RamsDocumentController::class, 'storeDocument'])->name('ramsdocuments.store');
        Route::get('/{document}', [RamsDocumentController::class, 'show'])->name('ramsdocuments.show');
        Route::get('/{document}/edit', [RamsDocumentController::class, 'edit'])->name('ramsdocuments.edit');
        Route::put('/{document}', [RamsDocumentController::class, 'update'])->name('ramsdocuments.update');
        Route::delete('/{document}', [RamsDocumentController::class, 'destroy'])->name('ramsdocuments.destroy');
        Route::get('/{id}/{level}/feedback', [RamsDocumentController::class, 'viewfeedback'])->name('ramsdocuments.feedback');
        Route::get('/{id}/{level}/combine', [RamsDocumentController::class, 'viewcombine'])->name('ramsdocuments.combine');
        Route::get('/{id}/{level}/smfeedback', [RamsDocumentController::class, 'viewsmfeedback'])->name('ramsdocuments.smfeedback');
        Route::get('/{id}/{level}/finalisasi', [RamsDocumentController::class, 'viewfinalisasi'])->name('ramsdocuments.finalisasi');
        Route::post('/{id}/feedbackcombine', [RamsDocumentController::class, 'submitFeedbackCombine'])->name('ramsdocuments.submitFeedbackCombine');
        Route::delete('/{documentId}/feedback/{feedbackId}', [RamsDocumentController::class, 'destroyFeedback'])->name('ramsdocuments.feedback.destroy');
        Route::post('/{documentId}/approve/{feedbackId}', [RamsDocumentController::class, 'approveFeedback'])->name('ramsdocuments.feedback.approve');
        Route::post('/{documentId}/reject/{feedbackId}', [RamsDocumentController::class, 'rejectFeedback'])->name('ramsdocuments.feedback.reject');
        Route::get('/{id}/sendSM', [RamsDocumentController::class, 'sendSM'])->name('ramsdocuments.sendSM');
    });
    // Rute untuk Forum
    Route::prefix('forums')->group(function () {
        Route::get('/', [ForumController::class, 'index'])->name('forums.index');
        Route::get('/show/{forum}', [ForumController::class, 'show'])->name('forums.show');
        Route::get('/create', [ForumController::class, 'create'])->name('forums.create');
        Route::post('/', [ForumController::class, 'store'])->name('forums.store');
        Route::post('/{forum}/chats', [ForumController::class, 'storeChat'])->name('forums.chats.store');
        Route::get('/{id}/chats', [ForumController::class, 'loadChats'])->name('forums.chats.load');
        Route::get('/{id}/newchats', [ForumController::class, 'loadNewChats'])->name('forums.newChats');
    });
    // Rute untuk Nodokumen
    Route::prefix('justi-memo')->group(function () {
        Route::get('/upload', [JustiMemoController::class, 'uploadForm']);
        Route::post('/upload', [JustiMemoController::class, 'uploadDocMTPR'])->name('justi-memo.upload');
        Route::get('/show/{memoId}', [JustiMemoController::class, 'showDocument'])->name('justi-memo.show');
        Route::get('/show/{memoId}/uploadfeedback', [JustiMemoController::class, 'documentfeedback'])->name('justi-memo.uploadfeedback');
    });




    // ---------- Bahan Contoh -------
    // ---------- Bahan Contoh -------
    // ---------- Bahan Contoh -------
    // ---------- Bahan Contoh -------
    Route::get('/', [HomeController::class, 'showHome']);
    Route::get('/slider', [HomeController::class, 'showHomeslider']);
    Route::get('/gps-upload', [GpsController::class, 'index'])->name('gps.index');
    Route::post('/gps-upload', [GpsController::class, 'upload'])->name('gps.upload');
    Route::get('/mindmap', [MindMapController::class, 'index'])->name('mindmap.index');
    Route::post('/mindmap', [MindMapController::class, 'mindmapstore'])->name('mindmap.store');
    Route::post('/mindmap-kind', [MindMapController::class, 'mindmapkindstore'])->name('mindmap-kind.store');
    //backup technology
    Route::get('/run-backup', [BackupController::class, 'runBackup']);
    Route::get('/download-backup', [BackupController::class, 'downloadBackup']);



    // ---------- Program belum dipetakan -------
    // ---------- Program belum dipetakan -------
    // ---------- Program belum dipetakan -------
    // ---------- Program belum dipetakan -------

    Route::post('/agenda', [NotulenController::class, 'agendastore'])->name('agenda.store');
    Route::get('/notulen/show/{id}', [NotulenController::class, 'show'])->name('notulen.show');
    Route::get('/notulen', [NotulenController::class, 'index'])->name('notulen.index');
    Route::post('/notulen/extract', [NotulenController::class, 'extractFromPdf'])->name('notulen.extract');
    Route::get('/notulen/export/{notulen_id}', [NotulenController::class, 'exportNotulenToExcel']);
    // Route untuk menghapus notulen
    Route::delete('/notulen/{id}', [NotulenController::class, 'destroy'])->name('notulen.destroy');
    Route::post('/notulen', [NotulenController::class, 'store'])->name('notulen.store');
    Route::put('/notulen/{id}', [NotulenController::class, 'update']);
    Route::post('/notulen/update', [NotulenController::class, 'updateissue'])->name('notulen.update');
    Route::post('/notulen/topicnotulen/store', [NotulenController::class, 'storetopicnotulen'])->name('notulen.storetopicnotulen');
    Route::post('/notulen/store-issue', [NotulenController::class, 'storeIssue'])->name('notulen.storeissue');
    Route::post('/notulen/store-solution', [NotulenController::class, 'storeSolution'])->name('notulen.storesolution');
    Route::put('/solution/{id}/toggle-status', [NotulenController::class, 'toggleSolutionStatus'])->name('solution.toggle-status');
    Route::get('/notulen/project-type/{projectTypeId}', [NotulenController::class, 'getNotulensByProjectType'])->name('notulen.byProjectType');
    Route::post('/notulen/uploadsistemku/', [NotulenController::class, 'importExcelsistem'])->name('notulen.updateexcel');
    Route::get('/notulen/upload', [NotulenController::class, 'upload'])->name('notulen.upload');
    Route::put('/notulen/solution/update/{id}', [NotulenController::class, 'updateSolution'])->name('notulen.solution.update');


    // Route untuk mengunduh file DOCX
    Route::get('/download-file', function (Request $request) {
        $path = 'public/' . $request->query('path'); // Tambahkan 'public/' untuk akses file di storage/app/public

        // Pastikan file ada sebelum diunduh
        if (Storage::exists($path)) {
            return response()->download(Storage::path($path));
        }

        // Jika file tidak ditemukan, tampilkan error 404
        abort(404, 'File not found');
    })->name('download.file');

    Route::get('/massuploaduser', [FileController::class, 'massuploaduser']);
    Route::post('/massuploaduser', [FileController::class, 'uploadmassuploaduser']);
    Route::get('/search-results', [FileController::class, 'searchMetadata'])->name('searchresult');
    Route::get('/search', [FileController::class, 'searchForm'])->name('searchview');
    Route::get('/file/aksi/upload', [FileController::class, 'showuploadfile']);
    Route::post('/file/aksi/upload', [FileController::class, 'postuploadfile'])->name('file.upload');
    Route::get('/file/{id}', [FileController::class, 'showMetadata'])->name('metadata.show');
    Route::get('/file/{id}/edit', [FileController::class, 'metadataedit'])->name('metadata.edit');
    Route::put('/file/{id}', [FileController::class, 'updateinformasimetadata'])->name('file.update');
    Route::get('/file', [FileController::class, 'showAllMetadata'])->name('metadata.all');
    Route::delete('/files/{id}', [FileController::class, 'deleteFile'])->name('file.delete');
    Route::delete('/file/deletefileMultiple', [FileController::class, 'deleteFileMultiple'])->name('file.deleteMultiple');
    Route::post('/document/deletedocumentMultiple', [FileController::class, 'deleteDocumentMultiple'])->name('document.deleteMultiple');
    Route::post('/document/reportMultiple', [FileController::class, 'reportDocumentMultiple'])->name('document.reportMultiple');
    Route::get('/previewdocument/{linkfile}', [FileController::class, 'previewDocument'])->name('document.preview');
    Route::get('/download/{id}', [FileController::class, 'downloadFile'])->name('file.download');

    // Untuk menggunakan method GET
    Route::get('/komat/update/{id}/{index}', [FileController::class, 'updatekomat']);
    Route::get('/komat/delete/{id}/{index}', [FileController::class, 'deletekomat']);
    // Untuk menggunakan method POST (jika lebih sesuai)
    Route::post('/komat/update/{id}/{index}', [FileController::class, 'updatekomat']);


    // Route untuk menampilkan form input kategori
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    // Route untuk menyimpan kategori baru
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    // Route untuk menampilkan semua kategori
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::delete('/categories/{categoryId}/members/{memberId}', [CategoryController::class, 'destroyMember'])->name('members.destroy');
    Route::post('/categories/{categoryId}/members', [CategoryController::class, 'storeMember'])->name('members.store');
    Route::get('/showsistem', [FileController::class, 'main2']);
    Route::get('ujicobabot', [BotTelegramController::class, 'informasichatbot']);
    Route::get('ujicobaKirimPesan', [BotTelegramController::class, 'ujicobaKirimPesan']);

    Route::get('/daily-notifications', [DailyNotificationController::class, 'index'])->name('daily-notifications.index');
    Route::get('/daily-notifications/show/{id}', [DailyNotificationController::class, 'show'])->name('daily-notifications.show');
    Route::get('/daily-notifications/download/{id}', [DailyNotificationController::class, 'downloadpdf'])->name('daily-notifications.downloadpdf');

    Route::get('/file/data', [FileManagementController::class, 'data'])->name('file.data');
    Route::get('/file/download/{id}', [FileManagementController::class, 'downloadFile'])->name('file.download');
    Route::get('file/showFile/{id}', [FileManagementController::class, 'showFile'])->name('file.showFile');
    Route::resource('/file', FileManagementController::class);

    Route::get('/edit-pdf', [PDFEditorController::class, 'form']);
    Route::post('/edit-pdf', [PDFEditorController::class, 'editPDF']);
    Route::get('/storage/{file}', function ($file) {
        return response()->download(storage_path('app/' . $file));
    });

    Route::get('/download-pdf', [PDFEditorController::class, 'editAndDownloadPDF']);
    Route::resource('wagroupnumbers', WagroupnumberController::class);
    Route::patch('wagroupnumbers/{wagroupnumber}/verify', [WagroupnumberController::class, 'verify'])->name('wagroupnumbers.verify');
});



// --------- Auth -------
// --------- Auth -------
// --------- Auth -------
// --------- Auth -------

//register,login
Route::get('/register662400023', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register662400023', [AuthController::class, 'registerform'])->name('registerklik');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);
Route::get('forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [AuthController::class, 'reset'])->name('password.update');
Route::get('numberverificator', [AuthController::class, 'numberverificator'])->name('auth.numberverificator');

// ---------- Program Otomasi Berjalan-------
// ---------- Program Otomasi Berjalan-------
// ---------- Program Otomasi Berjalan-------
// ---------- Program Otomasi Berjalan-------

//progressretrofit
Route::get('/newprogressreports/otomateprogressretrofit', [NewprogressreportController::class, 'otomateprogressretrofit'])->name('newprogressreports.otomateprogress');
// ppo dan fabrikasi
Route::get('/newprogressreports/whatsappsend', [NewprogressreportController::class, 'whatsappsend']);
Route::get('/newprogressreports/whatsappsendproject48bogie', [NewprogressreportController::class, 'whatsappsendproject48bogie']);
//telegram
Route::get('telegram-messages-accounts/runtelegram', [TelegramMessagesAccountController::class, 'runtelegram'])->name('telegram_messages_accounts.runtelegram');
Route::get('telegram-messages-accounts/notifMemo', [NewMemoController::class, 'notifMemo']);




Route::get('notifprojectmingguan', [NewreportController::class, 'notifprojectmingguan']);
Route::get('notifMemowhatsapp', [NewMemoController::class, 'notifMemowhatsapp']);
Route::get('generatenotifharian', [NewMemoController::class, 'generatenotifharian']);
Route::get('/send-report/{unit}/{date}', [NewMemoController::class, 'generateAndSendReport'])->name('send.report');
Route::get('telegram-messages-accounts/notifMemoforhighrank', [NewMemoController::class, 'notifMemoforhighrank']);
Route::get('ujiwa', [TelegramMessagesAccountController::class, 'ujiwa']);
// backup tiap hari
Route::get('/backupsql', [OtomasiController::class, 'run_simpandatabaseinka']);
Route::get('/download-last-backup', [OtomasiController::class, 'download_last_backup']);
Route::get('/log662400023', [OtomasiController::class, 'showLogs']);
// Allert Harian
Route::get('/document/allert', [HomeController::class, 'showAllmemoallert'])->name('allert.all');
Route::get('/document/allertunitall', [HomeController::class, 'memounitallert']);
Route::get('/cekhasil', [HomeController::class, 'progressall']);
//telegram
Route::get('/get-updates-telegramcommand', [OtomasiController::class, 'getUpdatestelegramcommand']);
Route::get('/backupsql', [OtomasiController::class, 'run_simpandatabaseinka']);

// jobticket update
Route::get('/jobticket/updatedocumentsupport', [JobticketController::class, 'updateInfoHari']);
Route::get('/jobticket/show/{jobticket_identity_part}/{jobticket_identity_id}/{jobticket_id}/{jobticketstartedrev_id}/{position}', [JobticketController::class, 'approvebywa'])->name('jobticket.approvebywa');
Route::get('/migrasijobticket', [JobticketController::class, 'migrasirelasi'])->name('migrasirelasi');

// jadwal
Route::get('/getschedule', [EventController::class, 'getschedule']);
Route::get('/events/show/{id}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/destroy/{id}', [EventController::class, 'destroy'])->name('events.destroy');
Route::post('/events/', [EventController::class, 'store'])->name('events.store');
Route::get('/download-daypilot-pdf', [EventController::class, 'downloadPDF'])->name('daypilot.index');



Route::get('/search-dokumen', [NewprogressreportController::class, 'searchdokumenbywa']);
Route::get('/streamdownloadfile', [FileController::class, 'streamdownloadfile'])->name('file.streamdownloadfile');
Route::get('/ujicoba', [NewprogressreportController::class, 'ujicoba']);
Route::get('/downloadganttchart/target/chart', [NewreportController::class, 'downloadChart']);
Route::get('/newreports/ganttchart/target/chart', [NewreportController::class, 'target'])->name('newreports.target');
Route::get('/newreports/ganttchart/target/chart/slideshow', [NewreportController::class, 'targetslideshow'])->name('newreports.target.slideshow');
Route::get('/newreports/areachart/jamorang/chart', [NewreportController::class, 'jamorang'])->name('newreports.jamorang');
Route::get('/ganttcharttenminutes/hasil/chart', [NewreportController::class, 'getProjectDatatenminutes'])->name('newreports.getProjectDatatenminutes');
Route::get('/ganttchart/hasil/chart', [NewreportController::class, 'getProjectData'])->name('newreports.getProjectData');
Route::get('/ganttchart/hasil/chart/onehour', [NewreportController::class, 'getProjectDataonehour'])->name('newreports.getProjectDataonehour');
Route::get('/areachart/hasil/chart', [NewreportController::class, 'getHoursProjectData'])->name('newreports.getHoursProjectData');
Route::get('/workloadproject', [NewprogressreportController::class, 'getproject'])->name('newreports.workloadproject');
Route::get('/telegram/get-updates', [OtomasiController::class, 'getUpdatesTelegramCommand']);
// pencarian umum
Route::get('/katalogkomat/search', [KatalogKomatController::class, 'searchKomat'])->name('katalogkomat.searchKomat');
Route::get('/unclearpdfdownload', [NewprogressreportController::class, 'unclearpdfdownload'])->name('newprogressreports.unclearpdfdownload');
