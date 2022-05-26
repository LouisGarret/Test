### XPATH partie théorique

1. Retourner tous les éléments book

```xpath
/library/book
```

2. Retourner tous les éléments title ayant comme parent un élément book avec un attribut type égal à roman

```xpath
//book[@type='roman']/title
```

3. Retourner le nombre d'éléments book ayant un attribut type égal à bd

```xpath
count(//*[@type='bd'])
```

4. Que renvoie la requête XPath suivante :  /library/library/ancestor-or-self::library

La requête renvoie toute les library ayant un parent library donc le livre toto 5. En raison de l'axes ancestor-or-self, nous cherchons également les parents et grands parent donc l'entiereté de notre document   

```xml


<library>
    <book>
        <title>toto1</title>
        <author>titi</author>
    </book>
    <book type="doc">
        <title>toto2</title>
        <author>titi</author>
    </book>
    <book type="roman">
        <title>toto3</title>
        <author>titi</author>
    </book>
    <book type="bd">
        <title>toto4</title>
        <author>titi2</author>
    </book>
    <library>
        <book type="roman">
            <title>toto5</title>
            <author>titi</author>
        </book>
    </library>
</library>
```
```xml
<library>
<book type="roman">
    <title>toto5</title>
    <author>titi</author>
</book>
</library>
```
