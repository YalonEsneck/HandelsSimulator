package trade;

import utils.IUniqueContainerItem;

public class StockItem implements IUniqueContainerItem {
	protected String name;
	protected Integer standardPrice;
	protected Integer maxPrice;
	protected Integer minPrice;
	protected Double currentPrice;

	public StockItem(String name, Integer standardPrice) {
		this.name = name;
		this.standardPrice = standardPrice;
		this.currentPrice = standardPrice.doubleValue();

		// Set highest possible price to 120%
		this.maxPrice = (int) Math.ceil(standardPrice * 1.2);

		// Set lowest possible price to 80%
		this.minPrice = (int) Math.ceil(standardPrice * .8);
	}

	public Double simulatePrice(Integer currentQuantity, Integer maxQuantity, Integer quota) {
		Double quantityFactor = this.getQuantityFactor(currentQuantity, maxQuantity, quota);
		Double totalFactor = quantityFactor;

		// Apply price factor to total factor because it provides caps.
		Double priceFactor = this.getPriceFactor(currentQuantity, quota);
		return currentPrice * (1.0 + totalFactor * priceFactor);
	}

	protected Double getPriceFactor(Integer currentQuantity, Integer quota) {
		if (currentQuantity < quota) {
			return 1.0 - this.currentPrice / this.maxPrice;
		} else {
			return 1.0 - this.minPrice / this.currentPrice;
		}
	}

	protected Double getQuantityFactor(Integer currentQuantity, Integer maxQuantity, Integer quota) {
		return Math.pow(1.0 - (currentQuantity.doubleValue() / quota.doubleValue()), 3.0);
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public Integer getStandardPrice() {
		return standardPrice;
	}

	public void setStandardPrice(Integer standardPrice) {
		this.standardPrice = standardPrice;
	}

	public Integer getMinPrice() {
		return minPrice;
	}

	public void setMinPrice(Integer minPrice) {
		this.minPrice = minPrice;
	}

	public Integer getMaxPrice() {
		return maxPrice;
	}

	public void setMaxPrice(Integer maxPrice) {
		this.maxPrice = maxPrice;
	}

	public Double getCurrentPrice() {
		return Math.ceil(this.currentPrice);
	}

	public void setCurrentPrice(Double currentPrice) {
		this.currentPrice = currentPrice;
	}

	@Override
	public String getUniqueValue() {
		return this.name.toLowerCase();
	}
}
