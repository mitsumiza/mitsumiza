import random
from datetime import datetime, timedelta


data = []
num_records = 10  

for _ in range(num_records):
    first_names = ["Мэри", "Джон", "Эмили", "Бобби", "Конни"]
    last_names = ["Генрих", "Керро", "Гецати", "Шепс", "Грей"]
    

    email = (
        f"{random.choice(first_names).lower()}"
        f".{random.choice(last_names).lower()}"
        f"{random.randint(10, 99)}@example.com"
    )
    

    birth_date = datetime.now() - timedelta(days=random.randint(365*18, 365*90))
    
    record = {
        "Имя": random.choice(first_names),
        "Фамилия": random.choice(last_names),
        "Email": email,
        "Дата рождения": birth_date.strftime("%Y-%m-%d"),
        "Город": random.choice(["Москва", "Санкт-Петербург", "Казань", "Новосибирск"]),
        "Телефон": f"+7{random.randint(9000000000, 9999999999)}"
    }
    data.append(record)


headers = ["Имя", "Фамилия", "Email", "Дата рождения", "Город", "Телефон"]
print(",".join(headers))  

for record in data:
    row = [
        record["Имя"],
        record["Фамилия"],
        record["Email"],
        record["Дата рождения"],
        record["Город"],
        record["Телефон"]
    ]
    print(",".join(row))