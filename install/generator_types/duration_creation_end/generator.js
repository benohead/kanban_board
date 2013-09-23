var startDate = parseDate(getAttributeValue(cardid, "cardCreationDate"));
var endDate = parseDate(getAttributeValue(cardid, "enddate"));
var value = "";
if (startDate && endDate) {
	value = "" + ((endDate-startDate)/(1000*60*60*24));
}
