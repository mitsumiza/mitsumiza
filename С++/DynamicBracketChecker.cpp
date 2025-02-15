
#include <iostream>
#include <stack>

using namespace std;

// Функция для проверки правильности расстановки скобок
bool checkBrackets(const string& expression) {
    stack<char> bracketStack;

    for (const char c : expression) {
        if (c == '(' || c == '[' || c == '{') {
            bracketStack.push(c); // Открывающая скобка - добавляем в стек
        }
        else if (c == ')' || c == ']' || c == '}') {
            if (bracketStack.empty()) {
                return false; // Закрывающая скобка без открывающей - ошибка
            }
            const char topBracket = bracketStack.top();
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
    string expression;

    cout << "Введите выражение: ";
    std::getline(cin, expression); // Ввод строки с выражением с помощью getline

    cout << "Выражение: " << expression << " - "
        << (checkBrackets(expression) ? "Valid" : "Invalid") << endl;

    return 0;
}
