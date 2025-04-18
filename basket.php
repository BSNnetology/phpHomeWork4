<?php

const OPERATION_EXIT = 0;
const OPERATION_PRINT = 1;
const OPERATION_ADD = 2;
const OPERATION_DELETE = 3;
const OPERATION_CHANGE = 4;
//-----------------------------

$operations = [
    OPERATION_EXIT => OPERATION_EXIT . '. Завершить программу.',
    OPERATION_PRINT => OPERATION_PRINT . '. Отобразить список покупок.',
    OPERATION_ADD => OPERATION_ADD . '. Добавить товар в список покупок.',
    OPERATION_DELETE => OPERATION_DELETE . '. Удалить товар из списка покупок.',
    OPERATION_CHANGE => OPERATION_CHANGE . '. Изменить товар в списке покупок.',
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
        case OPERATION_PRINT:
            showCurrentShoppingList();            
            break;

        case OPERATION_ADD:
            addIntoShoppingList();
            break;

        case OPERATION_DELETE:
            deleteFromShoppingList();
            break;

        case OPERATION_CHANGE:
            changeListItem();
            break;
    }

    echo "\n ----- \n";
} while ($operationNumber > 0);

//-----------------------------
echo 'Программа завершена' . PHP_EOL;

//-----------------------------
// Вывести текущий список покупок
function showShoppingList(): void {
    global $items; 

    if (count($items)) {
        echo "Ваш список покупок:\n\n"; 

        foreach( $items as $item => $item_name ) {
            echo 'Товар: '. $item .'  кол-во: '. $item_name . PHP_EOL;
        } 
        echo "\nВсего позиций: " . count($items) . "\n\n";
    } else {
        echo "Ваш список покупок пуст.\n\n";
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
            return $key < 3;
        });
    }

    echo 'Выберите операцию для выполнения: ' . PHP_EOL . PHP_EOL;       
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
// Проверить введенное количество
function checkNumericCount(mixed $currentCount): bool {
    if (!is_int($currentCount)) {
        echo <<<HEREDOC
        Количество товара введено некорректно. Повторите операцию.
        Нажмите enter для продолжения 
        HEREDOC; 

        fgets(STDIN);  
        return false;      
    }

    return true;
}


//-----------------------------
// Проверить отсутствие товара в списке
function checkExistName(string $currentName, array $curretntItems): bool {
    if (!array_key_exists($currentName, $curretntItems)) {
        echo <<<HEREDOC
        Указанный товар отсутствует в списке. Введите другую операцию.
        Нажмите enter для продолжения 
        HEREDOC;

        fgets(STDIN);
        return false;
    }

    return true;
}

//-----------------------------
// 1. Вывести текущий список покупок
function showCurrentShoppingList() {
    showShoppingList();
    echo 'Нажмите enter для продолжения';
    fgets(STDIN);
}

//-----------------------------
// 2. Добавить товар в список покупок
function addIntoShoppingList(): void {
    global $items; 

    echo "Введение название товара для добавления в список: \n> ";
    $itemName = trim(fgets(STDIN));
    
    if (array_key_exists($itemName, $items)) {
        echo <<<HEREDOC
        Указанный товар уже присутствует в списке. Введите другую операцию.
        Нажмите enter для продолжения \n
        HEREDOC; 
        fgets(STDIN);
        return;
    }

    echo "Введение количество товара\n";
    echo "(0 в список добавлено не будет):\n> ";
    fscanf(STDIN, "%d\n", $itemNCount);

    if (!checkNumericCount($itemNCount) || $itemNCount == 0) {
      return;
    } 

    $items[$itemName] = $itemNCount;
}

//-----------------------------
// 3. Удалить товар из списка покупок
function deleteFromShoppingList(): void {
    global $items; 

    echo "Введение название товара для удаления из списка:\n> ";
    $itemName = trim(fgets(STDIN));

    // Проверить, есть ли товары в списке? Если нет, то сказать об этом и попросить ввести другую операцию
    if (!checkExistName($itemName, $items)) {
        return;
    } else {
        unset($items[$itemName]);
    } 
}

//-----------------------------
// 4. Изменить товар в списке покупок
function changeListItem(): void {
    global $items; 

    echo "Введение название товара для изменения:\n> ";
    $itemName = trim(fgets(STDIN));

    // Проверить, есть ли товары в списке? Если нет, то сказать об этом и попросить ввести другую операцию
    if (!checkExistName($itemName, $items)) {
        return;
    }

    echo "Введение новое название товара\n";
    echo "(enter - оставить прежнее: $itemName)):\n> ";
    $newName = trim(fgets(STDIN));
    $isRewrite = $itemName !== $newName && $newName !== "";

    echo "Введение новое количество товара\n";
    echo "(enter - оставить прежнее: $items[$itemName]\n";
    echo"(0 - товар будет удален):\n> ";
    $newCount = trim(fgets(STDIN));
    
    
    if ($newCount === "") {
        $newCount = $items[$itemName]; 
    } 

    $newCount = is_numeric($newCount) ? intval($newCount) : $newCount;
    if (!checkNumericCount($newCount)) {       
        return;
    }

    if ($newName === "") {
        $newName = $itemName;
    } 

    $isRewrite = $isRewrite || ($newCount === 0);

    if ($isRewrite) {
        unset($items[$itemName]);
    } 

    if ($newCount === 0) {
        return;
    }

    $items[$newName] = $newCount;
}