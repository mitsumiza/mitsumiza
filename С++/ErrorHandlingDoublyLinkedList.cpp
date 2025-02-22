﻿#include <iostream>
#include <fstream>
#include <sstream>
#include <chrono>
#include <string>

// Класс для обработки ошибок
class ErrorHandler {
private:
    std::string message;
    std::chrono::system_clock::time_point time_point;

public:
    // Конструктор
    ErrorHandler(const std::string& message) : message(message), time_point(std::chrono::system_clock::now()) {}

    // Вывод ошибки в консоль и файл лога
    void printError() {
        auto time = std::chrono::duration_cast<std::chrono::milliseconds>(time_point.time_since_epoch()).count();
        std::cerr << "Ошибка: " << message << " (Время: " << time << "мс)" << std::endl;

        std::ofstream logFile("error_log.txt", std::ios::app);
        if (logFile.is_open()) {
            logFile << "Ошибка: " << message << " (Время: " << time << "мс)" << std::endl;
            logFile.close();
        }
    }
};

// Структура узла двусвязного списка
struct Node {
    int data;
    Node* prev;
    Node* next;

    Node(int data) : data(data), prev(nullptr), next(nullptr) {}
};

// Класс двусвязного списка
class DoublyLinkedList {
private:
    Node* head;
    Node* tail;

public:
    DoublyLinkedList() : head(nullptr), tail(nullptr) {}

    // Добавление элемента в конец
    void append(int data) {
        if (data > 100) { // Ограничение: значение не может превышать 100
            throw ErrorHandler("Ошибка: Значение элемента превышает допустимое ограничение.");
        }

        Node* newNode = new Node(data);
        if (head == nullptr) {
            head = newNode;
            tail = newNode;
        }
        else {
            tail->next = newNode;
            newNode->prev = tail;
            tail = newNode;
        }
    }

    // Получение итератора на первый элемент
    Node* begin() {
        if (head == nullptr) {
            throw ErrorHandler("Ошибка: Список пуст.");
        }
        return head;
    }
};

int main() {

    setlocale(LC_ALL, "RU");
    // 2.1 Тестирование добавления в двусвязный список
    DoublyLinkedList list;
    try {
        list.append(50);
        list.append(120); // Должно сработать исключение
    }
    catch (ErrorHandler& error) {
        error.printError();
    }

    // 2.2 Тестирование получения итератора
    try {
        auto it = list.begin();
        std::cout << "Первый элемент: " << it->data << std::endl;
    }
    catch (ErrorHandler& error) {
        error.printError();
    }

    // 2.3 Обработка файла
    std::string filename;
    std::cout << "Введите имя файла: ";
    std::cin >> filename;

    try {
        std::ifstream file(filename);
        if (!file.is_open()) {
            throw ErrorHandler("Ошибка: Не удалось открыть файл.");
        }

        std::string line;
        while (std::getline(file, line)) {
            std::istringstream iss(line);
            int num1, num2;
            if (!(iss >> num1 >> num2)) { // Проверка корректности строки
                throw ErrorHandler("Ошибка: Некорректная строка в файле.");
            }

            try {
                if (num2 == 0) { // Проверка на деление на ноль
                    throw ErrorHandler("Ошибка: Деление на ноль.");
                }
                std::cout << num1 << " / " << num2 << " = " << (double)num1 / num2 << std::endl;
            }
            catch (ErrorHandler& error) {
                error.printError();
            }
        }
        file.close();
    }
    catch (ErrorHandler& error) {
        error.printError();
    }

    return 0;
}
