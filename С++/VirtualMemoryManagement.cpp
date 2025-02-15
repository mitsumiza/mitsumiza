#include <iostream>
#include <Windows.h>

using namespace std;

int main() {
    setlocale(LC_ALL, "RU");
    // 1. Получение информации о системе, включая размер страницы
    SYSTEM_INFO systemInfo;
    GetSystemInfo(&systemInfo);
    size_t pageSize = systemInfo.dwPageSize;

    // 2. Запрос на резервирование виртуальной памяти
    cout << "Введите количество страниц для резервирования: ";
    size_t numPagesToReserve;
    cin >> numPagesToReserve;

    // Выделение виртуальной памяти с помощью VirtualAlloc
    // MEM_RESERVE: резервируется виртуальная память, но физическая память не выделяется
    // PAGE_READWRITE: атрибуты защиты для чтения и записи
    LPVOID reservedMemory = VirtualAlloc(nullptr, numPagesToReserve * pageSize, MEM_RESERVE, PAGE_READWRITE);

    // Обработка ошибки выделения памяти
    if (reservedMemory == nullptr) {
        cerr << "Ошибка: Не удалось зарезервировать виртуальную память." << endl;
        return 1; // Возврат кода ошибки
    }

    cout << "Адрес зарезервированной области: " << reservedMemory << endl;

    // 3. Запрос на выделение физической памяти (Commitment)
    cout << "Введите количество страниц для выделения: ";
    size_t numPagesToCommit;
    cin >> numPagesToCommit;

    cout << "Введите номер начальной страницы: ";
    size_t startingPage;
    cin >> startingPage;

    // Проверка корректности введенных значений
    if (startingPage > numPagesToReserve || startingPage + numPagesToCommit > numPagesToReserve) {
        cerr << "Ошибка: Некорректный номер начальной страницы или количество страниц." << endl;
        return 1;
    }

    // Рассчет адреса начала выделенной области памяти
    LPVOID committedMemory = (char*)reservedMemory + (startingPage - 1) * pageSize;

    // Выделение физической памяти с помощью VirtualAlloc
    // MEM_COMMIT: выделяется физическая память для уже зарезервированного пространства
    // PAGE_READWRITE: атрибуты защиты для чтения и записи
    committedMemory = VirtualAlloc(committedMemory, numPagesToCommit * pageSize, MEM_COMMIT, PAGE_READWRITE);

    // Обработка ошибки выделения памяти
    if (committedMemory == nullptr) {
        cerr << "Ошибка: Не удалось выделить физическую память." << endl;
        return 1;
    }

    cout << "Адрес выделенной области: " << committedMemory << endl;

    // 4. Копирование массива в выделенную область
    int array[] = { 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 };
    memcpy_s(committedMemory, numPagesToCommit * pageSize, array, sizeof(array));
    cout << "Скопированный массив: ";
    for (int i = 0; i < sizeof(array) / sizeof(array[0]); i++) {
        cout << ((int*)committedMemory)[i] << " ";
    }
    cout << endl;

    // 5. Получение информации о выделенной области с помощью VirtualQuery
    MEMORY_BASIC_INFORMATION memoryInfo;
    if (!VirtualQuery(committedMemory, &memoryInfo, sizeof(memoryInfo))) {
        cerr << "Ошибка: Не удалось получить информацию о выделенной области." << endl;
        return 1;
    }

    cout << "Информация о выделенной области: " << endl;
    cout << "State: " << (memoryInfo.State == MEM_COMMIT ? "MEM_COMMIT" : "MEM_FREE") << endl;
    cout << "Protect: " << (memoryInfo.Protect == PAGE_READWRITE ? "PAGE_READWRITE" : "Другой атрибут") << endl;

    // Получение информации об области сразу за выделенной
    LPVOID nextMemory = (char*)committedMemory + numPagesToCommit * pageSize;
    if (!VirtualQuery(nextMemory, &memoryInfo, sizeof(memoryInfo))) {
        cerr << "Ошибка: Не удалось получить информацию о следующей области." << endl;
        return 1;
    }

    cout << "Информация о следующей области: " << endl;
    cout << "State: " << (memoryInfo.State == MEM_COMMIT ? "MEM_COMMIT" : "MEM_FREE") << endl;
    cout << "Protect: " << (memoryInfo.Protect == PAGE_READWRITE ? "PAGE_READWRITE" : "Другой атрибут") << endl;

    // 6. Выделение новой страницы с атрибутом PAGE_READONLY
    LPVOID readonlyMemory = (char*)committedMemory + numPagesToCommit * pageSize;
    readonlyMemory = VirtualAlloc(readonlyMemory, pageSize, MEM_COMMIT, PAGE_READONLY);

    // Обработка ошибки выделения памяти
    if (readonlyMemory == nullptr) {
        cerr << "Ошибка: Не удалось выделить страницу с атрибутом PAGE_READONLY." << endl;
        return 1;
    }

    cout << "Адрес выделенной страницы с атрибутом PAGE_READONLY: " << readonlyMemory << endl;

    // Получение информации о странице с атрибутом PAGE_READONLY
    if (!VirtualQuery(readonlyMemory, &memoryInfo, sizeof(memoryInfo))) {
        cerr << "Ошибка: Не удалось получить информацию о странице с атрибутом PAGE_READONLY." << endl;
        return 1;
    }

    cout << "Информация о странице с атрибутом PAGE_READONLY: " << endl;
    cout << "State: " << (memoryInfo.State == MEM_COMMIT ? "MEM_COMMIT" : "MEM_FREE") << endl;
    cout << "Protect: " << (memoryInfo.Protect == PAGE_READONLY ? "PAGE_READONLY" : "Другой атрибут") << endl;

    // 7. Возврат выделенной памяти (Decommitment)
    cout << "Введите количество страниц для возврата: ";
    size_t numPagesToDecommit;
    cin >> numPagesToDecommit;

    cout << "Введите номер начальной страницы: ";
    size_t decommitStartingPage;
    cin >> decommitStartingPage;

    // Проверка корректности введенных значений
    if (decommitStartingPage > numPagesToReserve || decommitStartingPage + numPagesToDecommit > numPagesToReserve) {
        cerr << "Ошибка: Некорректный номер начальной страницы или количество страниц." << endl;
        return 1;
    }

    // Рассчет адреса начала области, из которой будет возвращена память
    LPVOID decommitMemory = (char*)reservedMemory + (decommitStartingPage - 1) * pageSize;

    // Возврат выделенной памяти с помощью VirtualFree
    // MEM_DECOMMIT: возвращается физическая память, но виртуальное пространство не освобождается
    if (!VirtualFree(decommitMemory, numPagesToDecommit * pageSize, MEM_DECOMMIT)) {
        cerr << "Ошибка: Не удалось вернуть выделенную память." << endl;
        return 1;
    }

    cout << "Адрес области, из которой память была возвращена: " << decommitMemory << endl;

    // Получение информации о возвращенной области
    if (!VirtualQuery(decommitMemory, &memoryInfo, sizeof(memoryInfo))) {
        cerr << "Ошибка: Не удалось получить информацию о возвращенной области." << endl;
        return 1;
    }

    cout << "Информация о возвращенной области: " << endl;
    cout << "State: " << (memoryInfo.State == MEM_COMMIT ? "MEM_COMMIT" : "MEM_FREE") << endl;
    cout << "Protect: " << (memoryInfo.Protect == PAGE_READWRITE ? "PAGE_READWRITE" : "Другой атрибут") << endl;

    // 8. Освобождение зарезервированной области
    if (!VirtualFree(reservedMemory, 0, MEM_RELEASE)) {
        cerr << "Ошибка: Не удалось освободить зарезервированную область." << endl;
        return 1;
    }

    cout << "Зарезервированная область успешно освобождена." << endl;

    return 0;
}