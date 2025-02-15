#include <iostream>
#include <cstdlib>
#include <ctime>
#include <windows.h>

using namespace std;

// Структура для представления массива строк
struct Array {
    int** data; // Указатель на массив указателей на строки
    int rows;   // Количество строк
    int* lengths; // Массив длин строк
};

// Функция для создания массива строк
Array createArray(int rows) {
    Array array;
    array.rows = rows;

    // Выделение памяти для массива указателей на строки
    array.data = (int**)HeapAlloc(GetProcessHeap(), HEAP_ZERO_MEMORY, sizeof(int*) * rows);
    if (array.data == nullptr) {
        cerr << "Ошибка выделения памяти для массива указателей на строки" << endl;
        exit(1);
    }

    // Выделение памяти для массива длин строк
    array.lengths = new int[rows];
    if (array.lengths == nullptr) {
        cerr << "Ошибка выделения памяти для массива длин строк" << endl;
        exit(1);
    }

    // Выделение памяти для каждой строки
    for (int i = 0; i < rows; i++) {
        int length = rand() % 10 + 1; // Случайная длина строки

        // Выделение памяти для строки с использованием HeapAlloc
        array.data[i] = (int*)HeapAlloc(GetProcessHeap(), HEAP_ZERO_MEMORY, sizeof(int) * length);
        if (array.data[i] == nullptr) {
            cerr << "Ошибка выделения памяти для строки " << i << endl;
            // Освобождение ранее выделенной памяти
            for (int j = 0; j < i; j++) {
                HeapFree(GetProcessHeap(), 0, array.data[j]);
            }
            HeapFree(GetProcessHeap(), 0, array.data);
            delete[] array.lengths;
            exit(1);
        }
        // Заполнение строки случайными числами
        for (int j = 0; j < length; j++) {
            array.data[i][j] = rand() % 10 + 1;
        }
        array.lengths[i] = length;
    }
    return array;
}

// Функция для сортировки массива строк по возрастанию их длин
void sortArray(Array& array) {
    // Сортировка методом "пузырька"
    for (int i = 0; i < array.rows - 1; i++) {
        for (int j = 0; j < array.rows - i - 1; j++) {
            if (array.lengths[j] > array.lengths[j + 1]) {
                // Обмен указателями на строки
                int* temp = array.data[j];
                array.data[j] = array.data[j + 1];
                array.data[j + 1] = temp;

                // Обмен длинами строк
                int tempLength = array.lengths[j];
                array.lengths[j] = array.lengths[j + 1];
                array.lengths[j + 1] = tempLength;
            }
        }
    }
}

// Функция для вывода массива строк
void printArray(const Array& array) {
    for (int i = 0; i < array.rows; i++) {
        for (int j = 0; j < array.lengths[i]; j++) {
            cout << array.data[i][j] << " ";
        }
        cout << endl;
    }
}

// Функция для освобождения памяти массива строк
void freeArray(Array& array) {
    for (int i = 0; i < array.rows; i++) {
        HeapFree(GetProcessHeap(), 0, array.data[i]); // Освобождаем память каждой строки
    }
    HeapFree(GetProcessHeap(), 0, array.data); // Освобождаем память массива указателей
    delete[] array.lengths; // Освобождаем память для массива длин строк
}

int main() {
    setlocale(LC_ALL, "RU");
    srand(static_cast<unsigned int>(time(nullptr))); // Инициализация генератора случайных чисел

    int rows;
    cout << "Введите количество строк: ";
    cin >> rows;
    if (rows < 5) {
        cerr << "Количество строк должно быть не менее 5" << endl;
        return 1;
    }

    // Создание массива строк
    Array array = createArray(rows);
    cout << "Массив:" << endl;
    printArray(array);

    // Сортировка массива строк по возрастанию длин
    sortArray(array);

    // Вывод массива строк
    cout << "Отсортированный массив:" << endl;
    printArray(array);

    // Освобождение памяти
    freeArray(array);

    return 0;
}
