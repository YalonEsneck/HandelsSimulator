package trade;

import java.util.HashMap;
import java.util.Map;

public class Market {
	private static final int piles = 8;
	private static final int capacityPerPile = 60;

	private HashMap<StockItem, Integer> stockItems;

	public Market() {
		this.stockItems = new HashMap<StockItem, Integer>();
	}

	public Integer addStockItem(StockItem stockItem, Integer quantity) throws Exception {

		// Check whether there is already a pile of this StockItem.
		if (this.stockItems.containsKey(stockItem)) {

			// Check the amount of items to add.
			if (quantity > Market.capacityPerPile) {
				return -1;
			} else {
				this.stockItems.put(stockItem, quantity + this.stockItems.get(stockItem));
				return this.stockItems.get(stockItem);
			}
		}

		// If there is no StockItem yet, attempt to create a new pile of it.
		else {

			// Check whether there is still enough space for another pile of StockItems.
			if (this.stockItems.size() < Market.piles) {

				// If a new pile can be created, ensure that it will not overflow immediately.
				if (quantity > Market.capacityPerPile) {
					return -1;
				} else {
					this.stockItems.put(stockItem, quantity);
					return this.stockItems.get(stockItem);
				}
			} else if (this.stockItems.size() == Market.piles) {
				return -1;
			} else {
				throw new Exception("Unexpected value: StockItems list is larger (" + this.stockItems.size()
						+ ") than allowed (" + Market.piles + ")!");
			}
		}
	}

	public Boolean removeStockItem() {
		return false;
	}

	public void tick() {
		for (Map.Entry<StockItem, Integer> stockItem : this.stockItems.entrySet()) {
			stockItem.getKey().setCurrentPrice(stockItem.getKey().simulatePrice(stockItem.getValue(),
					Market.capacityPerPile, Market.capacityPerPile / 2));
		}
	}

	public HashMap<StockItem, Integer> getStockItems() {
		return this.stockItems;
	}
}
