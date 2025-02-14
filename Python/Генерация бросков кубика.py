import random


cube = int(input('Введите количество граней у кубика: '))  
lot = int(input('Введите количество бросков: '))            
mod = int(input('Введите модификатор броска: ')) 

results = []  


for _ in range(lot):  
    roll = random.randint(1, cube) 
    results.append(roll)            


print(f"\nРезультаты бросков ({lot}d{cube} + {mod}):")
print("Отдельные броски:", ', '.join(map(str, results)))

total = sum(results) + mod  
print(f"Общий результат: {sum(results)} + {mod} = {total}")