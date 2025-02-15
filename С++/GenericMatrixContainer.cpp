#include <iostream>
#include <concepts>
#include <initializer_list>
#include <stdexcept> // Для std::runtime_error
#include <vector>

using namespace std;

// Концепт для ограничения типов
template <typename T>
concept MatrixType = requires(T a, T b) {
    { a + b } -> std::convertible_to<T>;
    { a* b } -> std::convertible_to<T>;
    { a * 1 } -> std::convertible_to<T>;
        requires(sizeof(T) <= 1000); // Ограничение размера
};

// Класс для представления матрицы
template <typename T>
class Matrix
{
private:
    std::vector<std::vector<T>> data; // std::vector для хранения данных
    int rows;  // Количество строк
    int cols;  // Количество столбцов

public:
    // Конструктор по умолчанию
    Matrix() : rows(0), cols(0) {}

    // Конструктор с заданными размерами
    Matrix(int r, int c) : rows(r), cols(c)
    {
        data.resize(rows, std::vector<T>(cols));
    }

    // Конструктор копирования для правильного копирования матриц
    Matrix(const Matrix<T>& other) : rows(other.rows), cols(other.cols), data(other.data) {}

    // Деструктор
    ~Matrix() {}

    // Метод для получения значения по индексу
    T& operator()(int row, int col)
    {
        if (row < 0 || row >= rows || col < 0 || col >= cols)
        {
            throw std::out_of_range("Index out of bounds");
        }
        return data[row][col];
    }

    // Метод для получения значения по индексу (const)
    const T& operator()(int row, int col) const
    {
        if (row < 0 || row >= rows || col < 0 || col >= cols)
        {
            throw std::out_of_range("Index out of bounds");
        }
        return data[row][col];
    }

    // Операции сложения и умножения
    Matrix<T> operator+(const Matrix<T>& other) const
    {
        if (rows != other.rows || cols != other.cols)
        {
            throw std::runtime_error("Matrices have different dimensions");
        }

        // Создайте новую матрицу для результата сложения
        Matrix<T> result(rows, cols);

        // Выполните сложение поэлементно
        for (int i = 0; i < rows; ++i) {
            for (int j = 0; j < cols; ++j) {
                result(i, j) = data[i][j] + other(i, j);
            }
        }

        return result;
    }

    Matrix<T> operator*(const Matrix<T>& other) const
    {
        if (cols != other.rows)
        {
            throw std::runtime_error("Incompatible matrix dimensions for multiplication");
        }

        Matrix<T> result(rows, other.cols);
        for (int i = 0; i < rows; ++i)
        {
            for (int j = 0; j < other.cols; ++j)
            {
                for (int k = 0; k < cols; ++k)
                {
                    result(i, j) += data[i][k] * other(k, j); // Используем += для накопления результата
                }
            }
        }
        return result;
    }

    // Умножение на целое число
    Matrix<T> operator*(int val) const
    {
        Matrix<T> result(rows, cols);
        for (int i = 0; i < rows; ++i)
        {
            for (int j = 0; j < cols; ++j)
            {
                result(i, j) = data[i][j] * val;
            }
        }
        return result;
    }

    // Метод для печати матрицы
    void print() const
    {
        for (int i = 0; i < rows; ++i)
        {
            for (int j = 0; j < cols; ++j)
            {
                cout << data[i][j] << " ";
            }
            cout << endl;
        }
    }

    // Перегрузка оператора << для вывода матрицы
    friend ostream& operator<<(ostream& out, const Matrix<T>& matrix) {
        for (size_t i = 0; i < matrix.rows; ++i) {
            for (size_t j = 0; j < matrix.cols; ++j) {
                out << matrix.data[i][j] << " ";
            }
            out << endl;
        }
        return out;
    }
};

// Динамическая структура контейнера
template <MatrixType T>
class DynamicContainer
{
private:
    struct Node
    {
        T data;
        Node* next;
        Node(const T& value) : data(value), next(nullptr) {}
    };

    Node* head;  // Указатель на первый элемент
    Node* tail;  // Указатель на последний элемент
    size_t size; // Размер контейнера

public:
    // Конструктор по умолчанию
    DynamicContainer() : head(nullptr), tail(nullptr), size(0) {}

    // Конструктор инициализации
    DynamicContainer(const std::initializer_list<T>& list) : head(nullptr), tail(nullptr), size(0)
    {
        for (const auto& value : list) {
            push_back(value);
        }
    }

    // Деструктор
    ~DynamicContainer() {
        clear();
    }

    // Добавление элемента в конец
    void push_back(const T& value)
    {
        Node* newNode = new Node(value);
        if (head == nullptr) {
            head = newNode;
            tail = newNode;
        }
        else {
            tail->next = newNode;
            tail = newNode;
        }
        ++size;
    }

    // Вставка элемента по индексу
    void insert(size_t index, const T& value)
    {
        if (index > size) {
            throw std::out_of_range("Index out of bounds");
        }

        if (index == 0) {
            push_front(value);
            return;
        }

        if (index == size) {
            push_back(value);
            return;
        }

        Node* newNode = new Node(value);
        Node* current = head;
        for (size_t i = 0; i < index - 1; ++i) {
            current = current->next;
        }
        newNode->next = current->next;
        current->next = newNode;
        ++size;
    }

    // Добавление элемента в начало
    void push_front(const T& value)
    {
        Node* newNode = new Node(value);
        if (head == nullptr) {
            head = newNode;
            tail = newNode;
        }
        else {
            newNode->next = head;
            head = newNode;
        }
        ++size;
    }

    // Удаление элемента по индексу
    void erase(size_t index)
    {
        if (index >= size) {
            throw std::out_of_range("Index out of bounds");
        }

        if (index == 0) {
            pop_front();
            return;
        }
        if (index == size - 1) {
            pop_back();
            return;
        }

        Node* current = head;
        for (size_t i = 0; i < index - 1; ++i) {
            current = current->next;
        }
        Node* temp = current->next;
        current->next = temp->next;
        delete temp;
        --size;
    }

    // Удаление первого элемента
    void pop_front()
    {
        if (head == nullptr) {
            return;
        }

        Node* temp = head;
        head = head->next;
        delete temp;
        --size;
        if (size == 0) {
            tail = nullptr;
        }
    }

    // Удаление последнего элемента
    void pop_back()
    {
        if (tail == nullptr) {
            return;
        }

        if (head == tail) {
            delete head;
            head = nullptr;
            tail = nullptr;
            --size;
            return;
        }

        Node* current = head;
        while (current->next != tail) {
            current = current->next;
        }
        delete tail;
        tail = current;
        tail->next = nullptr;
        --size;
    }

    // Очистка контейнера
    void clear()
    {
        while (head != nullptr) {
            pop_front();
        }
    }

    // Получение элемента по индексу
    T& operator[](size_t index)
    {
        if (index >= size) {
            throw std::out_of_range("Index out of bounds");
        }

        Node* current = head;
        for (size_t i = 0; i < index; ++i) {
            current = current->next;
        }
        return current->data;
    }

    // Получение элемента по индексу (const)
    const T& operator[](size_t index) const
    {
        if (index >= size) {
            throw std::out_of_range("Index out of bounds");
        }

        Node* current = head;
        for (size_t i = 0; i < index; ++i) {
            current = current->next;
        }
        return current->data;
    }

    // Получение размера контейнера
    size_t getSize() const
    {
        return size;
    }

    // Проверка на пустоту
    bool isEmpty() const
    {
        return size == 0;
    }

    // Вывод содержимого контейнера
    void print() const
    {
        Node* current = head;
        while (current != nullptr) {
            cout << current->data << " ";
            current = current->next;
        }
        cout << endl;
    }

    // Перегрузка оператора << для вывода содержимого контейнера
    friend ostream& operator<<(ostream& out, const DynamicContainer& container) {
        Node* current = container.head;
        while (current != nullptr) {
            out << current->data << " "; // Вывод матрицы через <<
            current = current->next;
        }
        return out;
    }
};

int main() {
    // Создание матрицы
    Matrix<int> matrix1(2, 3);
    matrix1(0, 0) = 1;
    matrix1(0, 1) = 2;
    matrix1(0, 2) = 3;
    matrix1(1, 0) = 4;
    matrix1(1, 1) = 5;
    matrix1(1, 2) = 6;

    // Создание другой матрицы
    Matrix<int> matrix2(3, 2);
    matrix2(0, 0) = 7;
    matrix2(0, 1) = 8;
    matrix2(1, 0) = 9;
    matrix2(1, 1) = 10;
    matrix2(2, 0) = 11;
    matrix2(2, 1) = 12;

    // Вывод матриц
    cout << "Матрица 1:" << endl;
    matrix1.print();
    cout << "Матрица 2:" << endl;
    matrix2.print();

    // Умножение матриц
    Matrix<int> result = matrix1 * matrix2;
    cout << "Результат умножения:" << endl;
    result.print();

    // Создание динамического контейнера
    DynamicContainer<Matrix<int>> container;

    // Добавление матриц в контейнер
    container.push_back(matrix1);
    container.push_back(matrix2);

    // Вывод содержимого контейнера
    cout << "Содержимое контейнера:" << endl;
    container.print();

    // Доступ к элементам контейнера по индексу
    cout << "Первый элемент контейнера:" << endl;
    container[0].print();

    // Удаление элемента из контейнера
    container.erase(1);

    // Вывод содержимого контейнера после удаления элемента
    cout << "Содержимое контейнера после удаления:" << endl;
    container.print();

    return 0;
}