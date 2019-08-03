package server;

import trade.Market;

public class MarketWorker implements Runnable {
	private Market market;

	public MarketWorker(Market market) {
		this.market = market;
	}

	@Override
	public void run() {
		while (true) {
			try {
				synchronized (market) {
					market.tick();
				}
				Thread.sleep(2000);
			} catch (InterruptedException e) {
				return;
			}
		}
	}

}
