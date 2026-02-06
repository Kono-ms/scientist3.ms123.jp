<pre style="padding: 1em; border: 1px solid #ccc; line-height: 1.5; font-size: 18px;">
<?php

/* メソッド */
/*
public __construct(string $version = "1.0", string $encoding = "")
public adoptNode(DOMNode $node): DOMNode|false
public append(DOMNode|string ...$nodes): void
public createAttribute(string $localName): DOMAttr|false
public createAttributeNS(?string $namespace, string $qualifiedName): DOMAttr|false
public createCDATASection(string $data): DOMCdataSection|false
public createComment(string $data): DOMComment
public createDocumentFragment(): DOMDocumentFragment
public createElement(string $localName, string $value = ""): DOMElement|false
public createElementNS(?string $namespace, string $qualifiedName, string $value = ""): DOMElement|false
public createEntityReference(string $name): DOMEntityReference|false
public createProcessingInstruction(string $target, string $data = ""): DOMProcessingInstruction|false
public createTextNode(string $data): DOMText
public getElementById(string $elementId): ?DOMElement
public getElementsByTagName(string $qualifiedName): DOMNodeList
public getElementsByTagNameNS(?string $namespace, string $localName): DOMNodeList
public importNode(DOMNode $node, bool $deep = false): DOMNode|false
public load(string $filename, int $options = 0): bool
public loadHTML(string $source, int $options = 0): bool
public loadHTMLFile(string $filename, int $options = 0): bool
public loadXML(string $source, int $options = 0): bool
public normalizeDocument(): void
public prepend(DOMNode|string ...$nodes): void
public registerNodeClass(string $baseClass, ?string $extendedClass): bool
public relaxNGValidate(string $filename): bool
public relaxNGValidateSource(string $source): bool
public replaceChildren(DOMNode|string ...$nodes): void
public save(string $filename, int $options = 0): int|false
public saveHTML(?DOMNode $node = null): string|false
public saveHTMLFile(string $filename): int|false
public saveXML(?DOMNode $node = null, int $options = 0): string|false
public schemaValidate(string $filename, int $flags = 0): bool
public schemaValidateSource(string $source, int $flags = 0): bool
public validate(): bool
public xinclude(int $options = 0): int|false
*/

if (file_exists('print.html')) {

	$html = file_get_contents('print.html');
	$dom = new DOMDocument();
	@$dom->loadHTML($html);
	//$xml = simplexml_load_string($dom->saveHTML());
	//var_dump($xml);

	// simple xml node
	$html = simplexml_import_dom($dom);
	var_dump($html->body->div);

	//foreach ($movies->movie[0]->rating as $rating) {
	//	switch((string) $rating['type']) { // 要素のインデックスとして、属性を取得します
	//	case 'thumbs':
	//		echo $rating, ' thumbs up';
	//		break;
	//	case 'stars':
	//		echo $rating, ' stars';
	//		break;
	//	}
	//}


} else {
    exit('Failed to open test.xml.');
}

?>
</pre>