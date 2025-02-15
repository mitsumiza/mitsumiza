def encoding(phrase, key):
    encrypted_phrase = ""
    for char in phrase:
        if char.isalpha():  
            shift = 65 if char.isupper() else 97  
            encrypted_char = chr((ord(char) - shift + key) % 26 + shift)  
            encrypted_phrase += encrypted_char
        else:
            encrypted_phrase += char  
    return encrypted_phrase


print("Введите строку для шифровки:")
phrase = input()
print("Введите ключ к шифру:")
key = int(input())


encrypted_phrase = encoding(phrase, key)
print("Зашифрованная строка:", encrypted_phrase)
