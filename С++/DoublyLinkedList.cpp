#include <iostream>

template <typename T>
struct Node {
    T data;
    Node* next;
    Node* prev;
    // устала
    // Конструктор узла
    Node(const T& value) : data(value), next(nullptr), prev(nullptr) {}
};

template <typename T>
class DoublyLinkedList {
private:
    Node<T>* head;
    Node<T>* tail;
    int size;

public:
    // Базовый конструктор
    DoublyLinkedList() : head(nullptr), tail(nullptr), size(0) {}

    // Конструктор копирования
    DoublyLinkedList(const DoublyLinkedList& other) : size(other.size) {
        if (other.head == nullptr) {
            head = nullptr;
            tail = nullptr;
        }
        else {
            head = new Node<T>(other.head->data);
            Node<T>* current = head;
            Node<T>* otherCurrent = other.head->next;

            while (otherCurrent != nullptr) {
                current->next = new Node<T>(otherCurrent->data);
                current->next->prev = current;
                current = current->next;
                otherCurrent = otherCurrent->next;
            }

            tail = current;
        }
    }

    // Конструктор перемещения
    DoublyLinkedList(DoublyLinkedList&& other) : head(other.head), tail(other.tail), size(other.size) {
        other.head = nullptr;
        other.tail = nullptr;
        other.size = 0;
    }

    // Оператор копирования
    DoublyLinkedList& operator=(const DoublyLinkedList& other) {
        if (this != &other) {
            clear();
            size = other.size;
            if (other.head == nullptr) {
                head = nullptr;
                tail = nullptr;
            }
            else {
                head = new Node<T>(other.head->data);
                Node<T>* current = head;
                Node<T>* otherCurrent = other.head->next;

                while (otherCurrent != nullptr) {
                    current->next = new Node<T>(otherCurrent->data);
                    current->next->prev = current;
                    current = current->next;
                    otherCurrent = otherCurrent->next;
                }

                tail = current;
            }
        }
        return *this;
    }

    // Оператор перемещения
    DoublyLinkedList& operator=(DoublyLinkedList&& other) {
        if (this != &other) {
            clear();
            head = other.head;
            tail = other.tail;
            size = other.size;

            other.head = nullptr;
            other.tail = nullptr;
            other.size = 0;
        }
        return *this;
    }

    // Деструктор
    ~DoublyLinkedList() {
        clear();
    }

    // Метод добавления в начало
    void push_front(const T& value) {
        Node<T>* newNode = new Node<T>(value);
        if (head == nullptr) {
            head = newNode;
            tail = newNode;
        }
        else {
            newNode->next = head;
            head->prev = newNode;
            head = newNode;
        }
        size++;
    }

    // Метод добавления в конец
    void push_back(const T& value) {
        Node<T>* newNode = new Node<T>(value);
        if (tail == nullptr) {
            head = newNode;
            tail = newNode;
        }
        else {
            tail->next = newNode;
            newNode->prev = tail;
            tail = newNode;
        }
        size++;
    }

    // Метод удаления из начала
    void pop_front() {
        if (head == nullptr) {
            return;
        }
        Node<T>* temp = head;
        head = head->next;
        if (head != nullptr) {
            head->prev = nullptr;
        }
        else {
            tail = nullptr;
        }
        delete temp;
        size--;
    }

    // Метод удаления из конца
    void pop_back() {
        if (tail == nullptr) {
            return;
        }
        Node<T>* temp = tail;
        tail = tail->prev;
        if (tail != nullptr) {
            tail->next = nullptr;
        }
        else {
            head = nullptr;
        }
        delete temp;
        size--;
    }

    // Метод очистки списка
    void clear() {
        while (head != nullptr) {
            Node<T>* temp = head;
            head = head->next;
            delete temp;
        }
        tail = nullptr;
        size = 0;
    }

    // Метод получения размера списка
    int getSize() const {
        return size;
    }

    // Метод проверки пустоты списка
    bool isEmpty() const {
        return head == nullptr;
    }

    // Метод получения элемента по индексу
    T get(int index) const {
        if (index < 0 || index >= size) {
            throw std::out_of_range("Индекс за пределами диапазона");
        }
        Node<T>* current = head;
        for (int i = 0; i < index; i++) {
            current = current->next;
        }
        return current->data;
    }

    // Метод вставки элемента по индексу
    void insert(int index, const T& value) {
        if (index < 0 || index > size) {
            throw std::out_of_range("Индекс за пределами диапазона");
        }
        if (index == 0) {
            push_front(value);
            return;
        }
        if (index == size) {
            push_back(value);
            return;
        }
        Node<T>* newNode = new Node<T>(value);
        Node<T>* current = head;
        for (int i = 0; i < index - 1; i++) {
            current = current->next;
        }
        newNode->next = current->next;
        newNode->prev = current;
        current->next->prev = newNode;
        current->next = newNode;
        size++;
    }

    // Метод удаления элемента по индексу
    void remove(int index) {
        if (index < 0 || index >= size) {
            throw std::out_of_range("Индекс за пределами диапазона");
        }
        if (index == 0) {
            pop_front();
            return;
        }
        if (index == size - 1) {
            pop_back();
            return;
        }
        Node<T>* current = head;
        for (int i = 0; i < index - 1; i++) {
            current = current->next;
        }
        Node<T>* temp = current->next;
        current->next = temp->next;
        temp->next->prev = current;
        delete temp;
        size--;
    }

    // Метод поиска элемента по значению
    int find(const T& value) const {
        Node<T>* current = head;
        int index = 0;
        while (current != nullptr) {
            if (current->data == value) {
                return index;
            }
            current = current->next;
            index++;
        }
        return -1; // Элемент не найден
    }

    // Метод вывода элементов списка
    void print() const {
        Node<T>* current = head;
        while (current != nullptr) {
            std::cout << current->data << " ";
            current = current->next;
        }
        std::cout << std::endl;
    }
};

int main() {
    DoublyLinkedList<int> list;

    setlocale(LC_ALL, "RU");
    // Добавление элементов
    list.push_back(1);
    list.push_front(2);
    list.push_back(3);

    // Вывод элементов
    std::cout << "Список: ";
    list.print();

    // Вставка элемента
    list.insert(1, 4);

    // Вывод элементов
    std::cout << "Список после вставки: ";
    list.print();

    // Удаление элемента
    list.remove(2);

    // Вывод элементов
    std::cout << "Список после удаления: ";
    list.print();

    // Поиск элемента
    int index = list.find(4);
    if (index != -1) {
        std::cout << "Элемент 4 найден по индексу: " << index << std::endl;
    }
    else {
        std::cout << "Элемент 4 не найден" << std::endl;
    }

    return 0;
}