var startDate = parseDate(getAttributeValue(cardid, "startdate"));
var endDate = parseDate(getAttributeValue(cardid, "enddate"));
var value = "";
if (startDate && endDate) {
	value = "" + ((endDate-startDate)/(1000*60*60*24));
}
