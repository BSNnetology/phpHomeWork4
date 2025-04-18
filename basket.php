<?php

const OPERATION_EXIT = 0;
const OPERATION_ADD = 1;
const OPERATION_DELETE = 2;
const OPERATION_PRINT = 3;
//-----------------------------

$operations = [
    OPERATION_EXIT => OPERATION_EXIT . '. Завершить программу.',
    OPERATION_ADD => OPERATION_ADD . '. Добавить товар в список покупок.',
    OPERATION_DELETE => OPERATION_DELETE . '. Удалить товар из списка покупок.',
    OPERATION_PRINT => OPERATION_PRINT . '. Отобразить список покупок.',
];
//-----------------------------

$items = [];
//-----------------------------

do {
    system('clear'); // system('cls'); // windows

    showShoppingList();
    $operationNumber = getOperationNumber($operations);

    system('clear');
    echo 'Выбрана операция: '  . $operations[$operationNumber] . PHP_EOL;

    switch ($operationNumber) {
        case OPERATION_ADD:
            addIntoShoppingList();
            break;

        case OPERATION_DELETE:
            deleteFromShoppingList();
            break;

        case OPERATION_PRINT:
            showCurrentShoppingList();            
            break;
    }

    echo "\n ----- \n";
} while ($operationNumber > 0);

echo 'Программа завершена' . PHP_EOL;

//-----------------------------
// Вывести текущий список товаров
function showShoppingList(): void {
    global $items; 

    if (count($items)) {
        echo 'Ваш список покупок: ' . PHP_EOL;   
        echo implode("\n", $items) . "\n";
        echo 'Всего позиций: ' . count($items) . "\n\n";
    } else {
        echo 'Ваш список покупок пуст.' . "\n\n";
    }; 
}

//-----------------------------
// Запрос на ввод номера операции
function getOperationNumber(array $workOperations): string {
    global $items; 

    // Проверить, есть ли товары в списке? Если нет, то не отображать пункт про удаление товаров
    if (!count($items)) {
        $workOperations = array_filter($workOperations, function($operation) use($workOperations) {
            $key = array_search($operation, $workOperations);
            return $key !== 2;
        });
    }

    echo 'Выберите операцию для выполнения: ' . PHP_EOL;       
    echo implode(PHP_EOL, $workOperations) . PHP_EOL . '> ';
    
    $operationNumber = trim(fgets(STDIN));

    if (!array_key_exists($operationNumber, $workOperations)) {
        system('clear');
        echo '!!! Неизвестный номер операции, повторите попытку.' . PHP_EOL;
        getOperationNumber($workOperations);
    }

    return $operationNumber;
}

//-----------------------------
// 1. Добавить товар в список товаров
function addIntoShoppingList(): void {
    global $items; 

    echo "Введение название товара для добавления в список: \n> ";
    $itemName = trim(fgets(STDIN));
    $items[] = $itemName;
}

//-----------------------------
// 2. Удалить товар из списка товаров
function deleteFromShoppingList(): void {
    global $items; 

    echo 'Введение название товара для удаления из списка:' . PHP_EOL . '> ';
    $itemName = trim(fgets(STDIN));

    $key = array_search($itemName, $items);

    // Проверить, есть ли товары в списке? Если нет, то сказать об этом и попросить ввести другую операцию
    if($key === false) {
        echo "Указанный товар отсутствует в списке. Введите другую операцию.\nНажмите enter для продолжения";
        fgets(STDIN);
    } else {
        unset($items[$key]);
    } 
}

//-----------------------------
// 3. Вывести список товаров
function showCurrentShoppingList() {
    showShoppingList();
    echo 'Нажмите enter для продолжения';
    fgets(STDIN);
}