<?php
// index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'config/database.php';
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard'; // تصحيح بسيط لتوجيه الرئيسية

// حماية النظام (مع السماح لروابط الـ QR للموبايل بالمرور للجمهور)
$public_actions = ['mobileReceipt', 'mobileStatement'];
if (!isset($_SESSION['user_id']) && $controller != 'auth' && !in_array($action, $public_actions)) {
    header("Location: ?controller=auth&action=login");
    exit();
}

ob_start();

// --- قسم التوجيه (Routing) ---
if ($controller == 'auth') {
    require_once 'controllers/AuthController.php';
    $auth = new AuthController();
    if ($action == 'login') $auth->login();
    elseif ($action == 'logout') $auth->logout();
}
// -- شئون العاملين --
elseif ($controller == 'staff') {
    require_once 'controllers/StaffController.php';
    $staff = new StaffController();

    if ($action == 'index') $staff->index();
    elseif ($action == 'import') $staff->import();
    elseif ($action == 'toggle' || $action == 'toggleStatus') $staff->toggleStatus();
    elseif ($action == 'createStaff') $staff->createStaff();
    elseif ($action == 'createSystemUser') $staff->createSystemUser();
    elseif ($action == 'edit') $staff->edit();
    elseif ($action == 'update') $staff->update();
}
// -- إدارة المستخدمين --
elseif ($controller == 'user') {
    require_once 'controllers/UserController.php';
    $user = new UserController();
    if ($action == 'index') $user->index();
    elseif ($action == 'create') $user->create();
    elseif ($action == 'toggle') $user->toggleStatus();
    elseif ($action == 'edit') $user->edit();
    elseif ($action == 'delete') $user->delete();
}
// -- شئون الطلاب --
elseif ($controller == 'student') {
    require_once 'controllers/StudentController.php';
    $student = new StudentController();
    if ($action == 'index') $student->index();
    elseif ($action == 'import') $student->import();
    elseif ($action == 'createPaymentOrder') $student->createPaymentOrder();
    elseif ($action == 'create') $student->create();
    elseif ($action == 'getSiblings') $student->getSiblings();
    // المسارات الجديدة:
    elseif ($action == 'profile') $student->profile();
    elseif ($action == 'downloadTemplate') $student->downloadTemplate();
    elseif ($action == 'archive') $student->archive();
    elseif ($action == 'edit') $student->edit();
    elseif ($action == 'update') $student->update();
    elseif ($action == 'academic_history') $student->academic_history();
    elseif ($action == 'statistics') $student->statistics();
}
// -- إعدادات النظام --
elseif ($controller == 'system') {
    require_once 'controllers/SystemController.php';
    $system = new SystemController();
    if ($action == 'settings') $system->settings();
}
// -- إدارة الأعوام الدراسية --
elseif ($controller == 'academic_year') {
    require_once 'controllers/AcademicYearController.php';
    $academic_year = new AcademicYearController();
    if ($action == 'index') $academic_year->index();
    elseif ($action == 'create') $academic_year->create();
    elseif ($action == 'update') $academic_year->update();
    elseif ($action == 'makeCurrent') $academic_year->makeCurrent();
    elseif ($action == 'rollover') $academic_year->rollover();
    elseif ($action == 'delete') $academic_year->delete();
}
// -- إدارة الخصومات --
elseif ($controller == 'discount') {
    require_once 'controllers/DiscountController.php';
    $discount = new DiscountController();
    if ($action == 'index') $discount->index();
    elseif ($action == 'saveDiscount') $discount->saveDiscount();
    elseif ($action == 'statement') $discount->statement();
    elseif ($action == 'sendStatementEmail') $discount->sendStatementEmail();
    elseif ($action == 'deleteDiscount') $discount->deleteDiscount(); // 🌟 سطر مسار حذف الخصم 🌟
}
// -- شباك التحصيل (الكاشير) --
elseif ($controller == 'invoice') {
    require_once 'controllers/InvoiceController.php';
    $invoice = new InvoiceController();
    if ($action == 'cashier') $invoice->cashier();
    elseif ($action == 'getPendingOrdersAjax') $invoice->getPendingOrdersAjax();
    elseif ($action == 'pay') $invoice->pay();
    elseif ($action == 'student_pay') $invoice->student_pay();
    elseif ($action == 'executeStudentPayment') $invoice->executeStudentPayment();
    elseif ($action == 'cancelOrder') $invoice->cancelOrder();
    elseif ($action == 'printReceipt') $invoice->printReceipt();
    elseif ($action == 'mobileReceipt') $invoice->mobileReceipt(); // 🌟 سطر رابط طباعة الموبايل
    elseif ($action == 'completeOrder') $invoice->completeOrder();
    elseif ($action == 'updateFinancialNote') $invoice->updateFinancialNote(); // 🌟 مسار تحديث الملاحظة المالية
    // 👇 السطرين  الخاصين بفوري 👇
    elseif ($action == 'fawryImport') $invoice->fawryImport();
    elseif ($action == 'processFawryImport') $invoice->processFawryImport();
    elseif ($action == 'setStartingSerial') $invoice->setStartingSerial();

    // 🌟 المسارات الجديدة للبحث السريع في الكاشير الدفع المباشر 🌟
    elseif ($action == 'searchGuardianAjax') $invoice->searchGuardianAjax();
    elseif ($action == 'createDirectOrder') $invoice->createDirectOrder();
}

// 🌟 -- يومية البنك (Excel) -- 🌟
elseif ($controller == 'bankExcel') {
    require_once 'controllers/BankExcelController.php';
    $bankExcel = new BankExcelController();
    if ($action == 'index') $bankExcel->index();
    elseif ($action == 'process_upload') $bankExcel->process_upload();
    elseif ($action == 'search_student') $bankExcel->search_student();
    elseif ($action == 'link_student') $bankExcel->link_student();
    elseif ($action == 'confirm_receipt') $bankExcel->confirm_receipt();
    // 🌟 المسارات الجديدة لاعتماد الكل والحذف 🌟
    elseif ($action == 'confirm_all') $bankExcel->confirm_all();
    elseif ($action == 'delete_pending') $bankExcel->delete_pending();
}

// -- الإعدادات المالية (البنود والتسعير) --
elseif ($controller == 'finance') {
    require_once 'controllers/FinanceController.php';
    $finance = new FinanceController();
    if ($action == 'feeItems') $finance->feeItems();
    elseif ($action == 'deleteItem') $finance->deleteItem();
    elseif ($action == 'feeStructure') $finance->feeStructure();
    elseif ($action == 'installments') $finance->installments();
    elseif ($action == 'saveInstallments') $finance->saveInstallments();
}
// --- شاشة التقارير والخزينة ---
elseif ($controller == 'report') {
    require_once 'controllers/ReportController.php';
    $report = new ReportController();
    if ($action == 'index') $report->index();
    elseif ($action == 'daily_treasury') $report->daily_treasury();
    elseif ($action == 'staff_children') $report->staff_children();
    elseif ($action == 'voidReceipt') $report->voidReceipt(); // 🌟 سطر مسار إلغاء وحذف الإيصال 🌟
    elseif ($action == 'dynamic_builder') $report->dynamic_builder(); // 🌟 سطر مُنشئ التقارير الجديد
    elseif ($action == 'debt_report') $report->debt_report();
    elseif ($action == 'zero_payments') $report->zero_payments();
    elseif ($action == 'debt_by_grade') $report->debt_by_grade();
    elseif ($action == 'siblings_report') $report->siblings_report();
    elseif ($action == 'cashier_summary') $report->cashier_summary();
    elseif ($action == 'heavy_debtors') $report->heavy_debtors();
    elseif ($action == 'credit_balances') $report->credit_balances();
    elseif ($action == 'voided_receipts') $report->voided_receipts();
    elseif ($action == 'revenue_by_item') $report->revenue_by_item();
    elseif ($action == 'checks_report') $report->checks_report();
    elseif ($action == 'student_statement') $report->student_statement();
    elseif ($action == 'total_discounts') $report->total_discounts();
    elseif ($action == 'books_delivery') $report->books_delivery();
    elseif ($action == 'bus_payments_report') $report->bus_payments_report();
    elseif ($action == 'fawry_export') $report->fawry_export();
    elseif ($action == 'suspended_staff') $report->suspended_staff();
    elseif ($action == 'user_activity') $report->user_activity();
    elseif ($action == 'top_grades') $report->top_grades();
    elseif ($action == 'collection_ratio') $report->collection_ratio();
    elseif ($action == 'year_comparison') $report->year_comparison();
    elseif ($action == 'parents_directory') $report->parents_directory();
    elseif ($action == 'audit_logs_report') $report->audit_logs_report();
}

// -- النسخ الاحتياطي والاستعادة --
elseif ($controller == 'backup') {
    require_once 'controllers/BackupController.php';
    $backup = new BackupController();
    if ($action == 'index') $backup->index();
    elseif ($action == 'exportSql') $backup->exportSql();
    elseif ($action == 'restoreSql') $backup->restoreSql();
    elseif ($action == 'exportZipExcel') $backup->exportZipExcel();
    elseif ($action == 'factoryReset') $backup->factoryReset();
}
// -- لوحة التحكم الرئيسية (ستعمل فقط إذا لم يطابق أي مما سبق) --
else {
    require_once 'controllers/HomeController.php';
    $home = new HomeController();
    $home->dashboard();
}

$content = ob_get_clean();

// --- فلترة إخراج البيانات ---
// 🌟 تأكد أن هذا السطر يحتوي على مسارات الإكسل 🌟
$ajax_actions = ['getSiblings', 'getPendingOrdersAjax', 'updateFinancialNote', 'process_upload', 'search_student', 'searchGuardianAjax'];

if (in_array($action, $ajax_actions)) {
    // إخراج البيانات الصافية فقط لطلبات الـ AJAX
    echo $content;
} elseif ($controller == 'auth' && $action == 'login') {
    // شاشة تسجيل الدخول ليس لها سيدبار
    echo $content;
} elseif (in_array($action, $public_actions)) {
    // شاشات الجمهور (مثل إيصال الموبايل) بدون سيدبار الإدارة
    echo $content;
} else {
    // باقي صفحات النظام التي تحتاج السيدبار والقوائم
    require_once 'views/layouts/main.php';
}
?>
?>
