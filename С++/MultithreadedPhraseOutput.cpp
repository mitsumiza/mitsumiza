#include <windows.h>
#include <iostream>
#include <string>
// Константы для количества итераций и времени ожидания
const int NUM_ITERATIONS = 10; // Количество раз, которое будут выводиться фразы
const int SLEEP_TIME = 100;     // Время ожидания в миллисекундах между выводом символов
// Глобальные переменные для хранения фраз
std::string phrase1 = "Фраза 1: Операционные системы";
std::string phrase2 = "Фраза 2: ВолГУ";
// 1. Функция для вывода фраз одновременно без синхронизации
DWORD WINAPI ThreadFunc1(LPVOID lpParam) {
    for (int i = 0; i < NUM_ITERATIONS; ++i) {
        std::cout << phrase1 << std::endl; // Вывод фразы 1
    }
    return 0; // Завершение потока
}
DWORD WINAPI ThreadFunc2(LPVOID lpParam) {
    for (int i = 0; i < NUM_ITERATIONS; ++i) {
        std::cout << phrase2 << std::endl; // Вывод фразы 2
    }
    return 0; // Завершение потока
}
// Функция для запуска потоков без синхронизации
void runWithoutSynchronization() {
    // Создание двух потоков
    HANDLE thread1 = CreateThread(NULL, 0, ThreadFunc1, NULL, 0, NULL);
    HANDLE thread2 = CreateThread(NULL, 0, ThreadFunc2, NULL, 0, NULL);

    // Ожидание завершения потоков
    WaitForSingleObject(thread1, INFINITE);
    WaitForSingleObject(thread2, INFINITE);

    // Закрытие дескрипторов потоков
    CloseHandle(thread1);
    CloseHandle(thread2);
}
// 2. Функция для вывода фраз с проблемой синхронизации
DWORD WINAPI ThreadFuncWithSleep1(LPVOID lpParam) {
    for (int i = 0; i < NUM_ITERATIONS; ++i) {
        for (char ch : phrase1) {
            std::cout << ch; // Вывод символа фразы 1
            Sleep(SLEEP_TIME); // Пауза между символами
        }
        std::cout << std::endl; // Переход на новую строку после фразы
    }
    return 0; // Завершение потока
}
DWORD WINAPI ThreadFuncWithSleep2(LPVOID lpParam) {
    for (int i = 0; i < NUM_ITERATIONS; ++i) {
        for (char ch : phrase2) {
            std::cout << ch; // Вывод символа фразы 2
            Sleep(SLEEP_TIME); // Пауза между символами
        }
        std::cout << std::endl; // Переход на новую строку после фразы
    }
    return 0; // Завершение потока
}
// Функция для запуска потоков с проблемой синхронизации
void runWithSynchronizationProblem() {
    HANDLE thread1 = CreateThread(NULL, 0, ThreadFuncWithSleep1, NULL, 0, NULL);
    HANDLE thread2 = CreateThread(NULL, 0, ThreadFuncWithSleep2, NULL, 0, NULL);

    // Ожидание завершения потоков
    WaitForSingleObject(thread1, INFINITE);
    WaitForSingleObject(thread2, INFINITE);

    // Закрытие дескрипторов потоков
    CloseHandle(thread1);
    CloseHandle(thread2);
}
// 3. Функция для синхронизации потоков с помощью критической секции
CRITICAL_SECTION criticalSection; // Объявление критической секции
// Функция для вывода фразы 1 с использованием критической секции
DWORD WINAPI ThreadFuncWithCriticalSection1(LPVOID lpParam) {
    for (int i = 0; i < NUM_ITERATIONS; ++i) {
        EnterCriticalSection(&criticalSection); // Вход в критическую секцию
        std::cout << phrase1 << std::endl; // Вывод фразы 1
        LeaveCriticalSection(&criticalSection); // Выход из критической секции
    }
    return 0; // Завершение потока
}
// Функция для вывода фразы 2 с использованием критической секции
DWORD WINAPI ThreadFuncWithCriticalSection2(LPVOID lpParam) {
    for (int i = 0; i < NUM_ITERATIONS; ++i) {
        EnterCriticalSection(&criticalSection); // Вход в критическую секцию
        std::cout << phrase2 << std::endl; // Вывод фразы 2
        LeaveCriticalSection(&criticalSection); // Выход из критической секции
    }
    return 0; // Завершение потока
}
// Функция для запуска потоков с критической секцией
void runWithCriticalSection() {
    InitializeCriticalSection(&criticalSection); // Инициализация критической секции
    HANDLE thread1 = CreateThread(NULL, 0, ThreadFuncWithCriticalSection1, NULL, 0, NULL);
    HANDLE thread2 = CreateThread(NULL, 0, ThreadFuncWithCriticalSection2, NULL, 0, NULL);

    // Ожидание завершения потоков
    WaitForSingleObject(thread1, INFINITE);
    WaitForSingleObject(thread2, INFINITE);

    // Закрытие дескрипторов потоков
    CloseHandle(thread1);
    CloseHandle(thread2);
    DeleteCriticalSection(&criticalSection); // Удаление критической секции
}
// 4. Функция для синхронизации потоков с помощью мютекса
HANDLE mutex; // Объявление мютекса
// Функция для вывода фразы 1 с использованием мютекса
DWORD WINAPI ThreadFuncWithMutex1(LPVOID lpParam) {
    for (int i = 0; i < NUM_ITERATIONS; ++i) {
        WaitForSingleObject(mutex, INFINITE); // Ожидание мютекса
        std::cout << phrase1 << std::endl; // Вывод фразы 1
        ReleaseMutex(mutex); // Освобождение мютекса
    }
    return 0; // Завершение потока
}
// Функция для вывода фразы 2 с использованием мютекса
DWORD WINAPI ThreadFuncWithMutex2(LPVOID lpParam) {
    for (int i = 0; i < NUM_ITERATIONS; ++i) {
        WaitForSingleObject(mutex, INFINITE); // Ожидание мютекса
        std::cout << phrase2 << std::endl; // Вывод фразы 2
        ReleaseMutex(mutex); // Освобождение мютекса
    }
    return 0; // Завершение потока
}
// Функция для запуска потоков с мютексом
void runWithMutex() {
    mutex = CreateMutex(NULL, FALSE, NULL); // Создание мютекса
    HANDLE thread1 = CreateThread(NULL, 0, ThreadFuncWithMutex1, NULL, 0, NULL);
    HANDLE thread2 = CreateThread(NULL, 0, ThreadFuncWithMutex2, NULL, 0, NULL);

    // Ожидание завершения потоков
    WaitForSingleObject(thread1, INFINITE);
    WaitForSingleObject(thread2, INFINITE);

    // Закрытие дескрипторов потоков и мютекса
    CloseHandle(thread1);
    CloseHandle(thread2);
    CloseHandle(mutex);
}
int main() {
    setlocale(LC_ALL, "RU");
    std::cout << "1. Запуск без синхронизации:" << std::endl;
    runWithoutSynchronization();
    std::cout << "\n2. Проблема синхронизации:" << std::endl;
    runWithSynchronizationProblem();
    std::cout << "\n3. Синхронизация с помощью критической секции:" << std::endl;
    runWithCriticalSection();
    std::cout << "\n4. Синхронизация с помощью мютекса:" << std::endl;
    runWithMutex();
    return 0;
}