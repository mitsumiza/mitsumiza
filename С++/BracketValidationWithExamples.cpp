﻿#include <iostream>
#include <stack>

using namespace std;

// Функция для проверки правильности расстановки скобок
bool checkBrackets(const string& expression) {
    stack<char> bracketStack;

    for (char c : expression) {
        if (c == '(' || c == '[' || c == '{') {
            bracketStack.push(c); // Открывающая скобка - добавляем в стек
        }
        else if (c == ')' || c == ']' || c == '}') {
            if (bracketStack.empty()) {
                return false; // Закрывающая скобка без открывающей - ошибка
            }
            char topBracket = bracketStack.top();
            bracketStack.pop(); // Удаляем последнюю открывающую скобку
            if ((c == ')' && topBracket != '(') ||
                (c == ']' && topBracket != '[') ||
                (c == '}' && topBracket != '{')) {
                return false; // Несоответствие типа скобок - ошибка
            }
        }
    }

    return bracketStack.empty(); // Если стек пуст - скобки расставлены правильно
}

int main() {
    string expression1 = "({[]})";
    string expression2 = "(]";
    string expression3 = "{[()]}";

    cout << "Expression 1: " << expression1 << " - "
        << (checkBrackets(expression1) ? "Valid" : "Invalid") << endl;

    cout << "Expression 2: " << expression2 << " - "
        << (checkBrackets(expression2) ? "Valid" : "Invalid") << endl;

    cout << "Expression 3: " << expression3 << " - "
        << (checkBrackets(expression3) ? "Valid" : "Invalid") << endl;

    return 0;
}