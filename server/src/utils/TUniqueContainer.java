package utils;

/**
 * Unique containers hold unique objects. Objects define their own uniqueness
 * which the unique container will check when adding.
 * 
 * @author Jan Merkelbag
 */
public interface TUniqueContainer {

	/**
	 * Tells whether the unique item passed already exists in the container. To
	 * check the existence the items define their unique identifiers.
	 * 
	 * @param newItem The item to check for existence.
	 * @return Returns TRUE if item exists, else FALSE.
	 */
	default boolean uniqueExists(IUniqueContainerItem newItem) {
		for (IUniqueContainerItem item : this.getItems()) {
			if (newItem.getUniqueValue() == item.getUniqueValue()) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @return All unique items held by this container as a simple array.
	 */
	IUniqueContainerItem[] getItems();
}
