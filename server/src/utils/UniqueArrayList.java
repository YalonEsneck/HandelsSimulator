package utils;

import java.util.ArrayList;

public class UniqueArrayList<E> extends ArrayList<E> implements TUniqueContainer {

	private static final long serialVersionUID = -8058075778658293143L;

	public boolean add(E e) {
		IUniqueContainerItem i = (IUniqueContainerItem) e;
		if (!this.uniqueExists(i)) {
			return super.add(e);
		} else {
			return false;
		}
	}

	@Override
	public IUniqueContainerItem[] getItems() {
		IUniqueContainerItem[] arr = new IUniqueContainerItem[super.size()];
		for (int i = 0; i < super.size(); i++) {
			arr[i] = (IUniqueContainerItem) super.get(i);
		}
		return arr;
	}
}
