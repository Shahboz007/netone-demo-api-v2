<?php

// Auth
require __DIR__ . "/api/auth/auth.php";
require __DIR__ . "/api/auth/user.php";
// Common
require __DIR__ . "/api/common/role.php";
require __DIR__ . "/api/common/userControl.php";
require __DIR__ . "/api/common/amountType.php";
require __DIR__ . "/api/common/amountSettings.php";
require __DIR__ . "/api/common/product.php";
require __DIR__ . "/api/common/receiveProduct.php";
require __DIR__ . "/api/common/receiveReturn.php";
require __DIR__ . "/api/common/customer.php";
require __DIR__ . "/api/common/supplier.php";
require __DIR__ . "/api/common/productStock.php";
require __DIR__ . "/api/common/order.php";
require __DIR__ . "/api/common/orderCancel.php";
require __DIR__ . "/api/common/expense.php";
require __DIR__ . "/api/common/wallet.php";
require __DIR__ . "/api/common/orderReturn.php";

// Production
require __DIR__ . "/api/production/recipe.php";
require __DIR__ . "/api/production/process.php";
require __DIR__ . "/api/production/checkStock.php";

// Finance
require __DIR__ . "/api/finance/currency.php";
require __DIR__ . "/api/finance/exchangeRate.php";
require __DIR__ . '/api/finance/paymentCustomer.php';
require __DIR__ . '/api/finance/userWallet.php';
require __DIR__ . '/api/finance/paymentExpense.php';
require __DIR__ . "/api/finance/paymentSupplier.php";
require __DIR__ . "/api/finance/getMoney.php";
require __DIR__ . "/api/finance/paymentGetMoney.php";
require __DIR__ . "/api/finance/paymentSetMoney.php";

// Statement
require __DIR__ . '/api/statement/statement.php';

// Chart
require __DIR__ . '/api/chart/producer.php';
